<?php
/*
 * traitement_devis.php — Reçoit le formulaire de devis.php
 *
 * Le visiteur ne voit jamais cette page : elle travaille puis le renvoie
 * vers devis.php. Ordre des contrôles :
 *   1. bonne méthode HTTP (POST)
 *   2. anti-robot (honeypot)
 *   3. jeton CSRF
 *   4. lecture des champs + anti-flood
 *   5. VALIDATION de chaque champ (la vraie barrière, le HTML n'est qu'un confort)
 *   6. enregistrement en base (requête préparée)
 *   7. mails (client + service commercial)
 *   8. redirection
 */
require_once __DIR__ . '/includes/securite.php';      // en premier : démarre la session
require_once __DIR__ . '/includes/referentiels.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/includes/fonctions.php';     // genererNumeroSuivi()

// Renvoie le visiteur sur le formulaire avec ses erreurs et ce qu'il avait saisi
// (stockés en session : c'est un "message flash", lu une seule fois par devis.php).
function retourAvecErreurs(array $erreurs, array $saisie = []): void
{
    $_SESSION['devis_erreurs'] = $erreurs;
    $_SESSION['devis_saisie']  = $saisie;
    header('Location: devis.php#formulaire');
    exit;
}

// 1. Accès direct par l'URL (GET) : rien à traiter
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: devis.php');
    exit;
}

// 2. Robot : on ne lui dit pas qu'il est démasqué, on le renvoie simplement
if (est_un_robot()) {
    header('Location: devis.php');
    exit;
}

// 3. CSRF
if (!csrf_verifier()) {
    retourAvecErreurs(['Votre session a expiré. Merci de renvoyer le formulaire.']);
}

// 4. Lecture des champs (aucun htmlspecialchars ici : on garde la donnée brute)
$saisie = [
    'nom'       => lire_texte('nom'),
    'prenom'    => lire_texte('prenom'),
    'telephone' => lire_texte('telephone'),
    'email'     => lire_texte('email'),
    'adresse'   => lire_texte('adresse'),
    'ville'     => lire_texte('ville'),
    'message'   => lire_texte('message'),
    'date_rdv'  => lire_texte('date_rdv'),
    'heure_rdv' => lire_texte('heure_rdv'),
    'types'     => lire_liste('type_nettoyage'),
    'rgpd'      => isset($_POST['rgpd']),
];

// Anti-flood : une demande par minute et par visiteur
if (isset($_SESSION['devis_dernier_envoi']) && time() - $_SESSION['devis_dernier_envoi'] < 60) {
    retourAvecErreurs(['Une demande vient déjà d\'être envoyée. Merci de patienter une minute.'], $saisie);
}

// 5. Validation
$erreurs = [];

// Lettres de toutes langues (\p{L}), espaces, apostrophes, tirets. Le "u" final = UTF-8.
$regexNom = "/^\p{L}[\p{L}\s'’\-]*$/u";

if ($saisie['nom'] === '' || mb_strlen($saisie['nom']) > 100 || !preg_match($regexNom, $saisie['nom'])) {
    $erreurs[] = 'Nom invalide (lettres, espaces, apostrophes et tirets uniquement).';
}
if ($saisie['prenom'] === '' || mb_strlen($saisie['prenom']) > 100 || !preg_match($regexNom, $saisie['prenom'])) {
    $erreurs[] = 'Prénom invalide (lettres, espaces, apostrophes et tirets uniquement).';
}

// 10 à 20 caractères : chiffres, espaces, points, tirets, avec un + optionnel au début
if (!preg_match('/^\+?[0-9 .\-]{10,20}$/', $saisie['telephone'])) {
    $erreurs[] = 'Numéro de téléphone invalide.';
}

// filter_var vérifie le format d'une adresse email selon la norme
if (mb_strlen($saisie['email']) > 150 || !filter_var($saisie['email'], FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = 'Adresse email invalide.';
}

// Les tailles max correspondent aux colonnes de la base (VARCHAR(255), VARCHAR(100)...)
if (mb_strlen($saisie['adresse']) > 255) {
    $erreurs[] = 'Adresse trop longue.';
}
if (mb_strlen($saisie['ville']) > 100) {
    $erreurs[] = 'Ville trop longue.';
}
if (mb_strlen($saisie['message']) > 2000) {
    $erreurs[] = 'Message trop long (2000 caractères maximum).';
}

// Types : chaque clé reçue doit exister dans la liste autorisée (liste blanche)
$typesLibelles = [];
foreach ($saisie['types'] as $cle) {
    if (!array_key_exists($cle, TYPES_NETTOYAGE)) {
        $erreurs[] = 'Type de nettoyage inconnu.';
        break;
    }
    $typesLibelles[] = TYPES_NETTOYAGE[$cle];
}
$typesLibelles = array_unique($typesLibelles);

// Date : format AAAA-MM-JJ réel, entre demain et +3 mois, pas un dimanche
$date    = DateTime::createFromFormat('!Y-m-d', $saisie['date_rdv']);
$demain  = new DateTime('tomorrow');
$dateMax = (new DateTime('today'))->modify('+' . DELAI_MAX_RDV_JOURS . ' days');

if (!$date || $date->format('Y-m-d') !== $saisie['date_rdv']) {
    $erreurs[] = 'Date de rendez-vous invalide.';
} elseif ($date < $demain || $date > $dateMax) {
    $erreurs[] = 'La date doit être comprise entre demain et dans 3 mois.';
} elseif ($date->format('N') === '7') {   // N : 1 = lundi ... 7 = dimanche
    $erreurs[] = 'Nous n\'intervenons pas le dimanche.';
}

// Créneau : doit être EXACTEMENT un des créneaux prévus (true = comparaison stricte)
if (!in_array($saisie['heure_rdv'], CRENEAUX, true)) {
    $erreurs[] = 'Créneau invalide.';
}

// Consentement RGPD obligatoire
if (!$saisie['rgpd']) {
    $erreurs[] = 'Vous devez accepter le traitement de vos données pour envoyer la demande.';
}

if ($erreurs) {
    retourAvecErreurs($erreurs, $saisie);
}

// 6. Enregistrement en base
$numeroSuivi = genererNumeroSuivi($pdo);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO devis (numero_suivi, nom, prenom, telephone, email, adresse, ville,
                            type_nettoyage, message, date_rdv, heure_rdv, statut)
         VALUES (:numero_suivi, :nom, :prenom, :telephone, :email, :adresse, :ville,
                 :type_nettoyage, :message, :date_rdv, :heure_rdv, 'nouvelle')"
    );
    $stmt->execute([
        ':numero_suivi'   => $numeroSuivi,
        ':nom'            => $saisie['nom'],
        ':prenom'         => $saisie['prenom'],
        ':telephone'      => $saisie['telephone'],
        ':email'          => $saisie['email'],
        ':adresse'        => $saisie['adresse'],
        ':ville'          => $saisie['ville'],
        ':type_nettoyage' => implode(', ', $typesLibelles),
        ':message'        => $saisie['message'],
        ':date_rdv'       => $saisie['date_rdv'],
        ':heure_rdv'      => $saisie['heure_rdv'],
    ]);
} catch (PDOException $e) {
    // Le détail technique va dans le journal du serveur, jamais à l'écran du visiteur
    error_log('Erreur insertion devis : ' . $e->getMessage());
    retourAvecErreurs(['Une erreur technique est survenue. Merci de réessayer ou de nous appeler.'], $saisie);
}

$_SESSION['devis_dernier_envoi'] = time();

// 7. Mails : confirmation au client + notification au service commercial (exigence du cahier des charges).
// Un échec d'envoi est journalisé dans mailer.php mais ne bloque pas le client : sa demande est enregistrée.
envoyerEmailConfirmationDevis($saisie['email'], $saisie['prenom'], $numeroSuivi, $saisie['date_rdv'], $saisie['heure_rdv']);
envoyerNotificationCommercial([
    'numero_suivi' => $numeroSuivi,
    'nom'          => $saisie['nom'],
    'prenom'       => $saisie['prenom'],
    'telephone'    => $saisie['telephone'],
    'email'        => $saisie['email'],
    'adresse'      => $saisie['adresse'],
    'ville'        => $saisie['ville'],
    'types'        => implode(', ', $typesLibelles),
    'date_rdv'     => $saisie['date_rdv'],
    'heure_rdv'    => $saisie['heure_rdv'],
    'message'      => $saisie['message'],
]);

// 8. Succès : le numéro passe par la session (et plus par l'URL, que n'importe qui peut modifier)
$_SESSION['devis_numero'] = $numeroSuivi;
header('Location: devis.php#formulaire');
exit;