<?php
/*
 * traitement_contact.php : Reçoit le formulaire de contact.php
 *
 * Mêmes protections que traitement_devis.php : POST uniquement, champ piège,
 * jeton CSRF, limite d'envoi, validation de chaque champ, requête préparée.
 * Les données sont stockées BRUTES (sans htmlspecialchars) : l'échappement se
 * fait au moment de l'affichage, avec e().
 */
require_once __DIR__ . '/includes/securite.php';
require_once __DIR__ . '/config.php';

function retourContact(array $erreurs, array $saisie = []): void
{
    $_SESSION['contact_erreurs'] = $erreurs;
    $_SESSION['contact_saisie']  = $saisie;
    header('Location: contact.php#formulaire');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

if (est_un_robot()) {
    header('Location: contact.php');
    exit;
}

if (!csrf_verifier()) {
    retourContact(['Votre session a expiré. Merci de renvoyer le formulaire.']);
}

$saisie = [
    'prenom'    => lire_texte('prenom'),
    'nom'       => lire_texte('nom'),
    'email'     => lire_texte('email'),
    'telephone' => lire_texte('telephone'),
    'message'   => lire_texte('message'),
];

if (isset($_SESSION['contact_dernier_envoi']) && time() - $_SESSION['contact_dernier_envoi'] < 60) {
    retourContact(['Un message vient déjà d\'être envoyé. Merci de patienter une minute.'], $saisie);
}

$erreurs  = [];
$regexNom = "/^\p{L}[\p{L}\s'’\-]*$/u";

if ($saisie['prenom'] === '' || mb_strlen($saisie['prenom']) > 100 || !preg_match($regexNom, $saisie['prenom'])) {
    $erreurs[] = 'Prénom invalide (lettres, espaces, apostrophes et tirets uniquement).';
}
if ($saisie['nom'] === '' || mb_strlen($saisie['nom']) > 100 || !preg_match($regexNom, $saisie['nom'])) {
    $erreurs[] = 'Nom invalide (lettres, espaces, apostrophes et tirets uniquement).';
}
if (mb_strlen($saisie['email']) > 150 || !filter_var($saisie['email'], FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = 'Adresse email invalide.';
}
// Téléphone facultatif : on ne le vérifie que s'il est rempli
if ($saisie['telephone'] !== '' && !preg_match('/^\+?[0-9 .\-]{10,20}$/', $saisie['telephone'])) {
    $erreurs[] = 'Numéro de téléphone invalide.';
}
if ($saisie['message'] === '' || mb_strlen($saisie['message']) > 2000) {
    $erreurs[] = 'Le message est obligatoire (2000 caractères maximum).';
}

if ($erreurs) {
    retourContact($erreurs, $saisie);
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO contacts (prenom, nom, email, telephone, message)
         VALUES (:prenom, :nom, :email, :telephone, :message)'
    );
    $stmt->execute([
        ':prenom'    => $saisie['prenom'],
        ':nom'       => $saisie['nom'],
        ':email'     => $saisie['email'],
        ':telephone' => $saisie['telephone'],
        ':message'   => $saisie['message'],
    ]);
} catch (PDOException $e) {
    error_log('Erreur insertion contact : ' . $e->getMessage());
    retourContact(['Une erreur technique est survenue. Merci de réessayer ou de nous appeler.'], $saisie);
}

$_SESSION['contact_dernier_envoi'] = time();

// Le message est transmis au commercial par e-mail. S'il échoue, le message reste
// en base et apparaît dans l'admin (Rappels et messages) : rien n'est perdu.
require_once __DIR__ . '/mailer.php';
envoyerNotificationContact($saisie);
$_SESSION['contact_succes'] = true;
header('Location: contact.php#formulaire');
exit;