<?php
/*
 * admin/traitement_update.php — Enregistre le statut, le montant et les PDF d'une demande
 *
 * Ce qui change par rapport à avant :
 *   - jeton CSRF obligatoire : un autre site ne peut pas modifier une demande à votre place ;
 *   - le montant est vérifié (nombre entre 0 et 100 000) ;
 *   - un PDF refusé (trop gros, pas un vrai PDF...) donne un message d'erreur
 *     au lieu d'être ignoré en silence ;
 *   - les PDF sont enregistrés avec un nom construit par nous, jamais celui envoyé.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/referentiels.php';   // CRENEAUX

/** Message à afficher sur la fiche, puis retour à la fiche */
function retour(int $id, string $type, string $message): never
{
    $_SESSION['admin_flash'] = ['type' => $type, 'message' => $message];
    header('Location: ' . ($id > 0 ? 'devis.php?id=' . $id : 'index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;

if (!csrf_verifier()) {
    retour($id, 'erreur', 'La page a expiré. Rechargez-la puis recommencez.');
}

// Statut : uniquement une valeur de notre liste
$statut = $_POST['statut'] ?? '';
if (!is_string($statut) || !array_key_exists($statut, ADMIN_STATUTS)) {
    retour($id, 'erreur', 'Statut inconnu.');
}

// Montant : vide = pas encore de montant ; sinon un nombre entre 0 et 100 000
$montant = trim((string) ($_POST['montant_devis'] ?? ''));
if ($montant === '') {
    $montant = null;
} else {
    $montant = filter_var(str_replace(',', '.', $montant), FILTER_VALIDATE_FLOAT);
    if ($montant === false || $montant < 0 || $montant > 100000) {
        retour($id, 'erreur', 'Le montant doit être un nombre entre 0 et 100 000 €.');
    }
    $montant = round($montant, 2);
}

// Intervention : date au format AAAA-MM-JJ (champ date du navigateur) et créneau de notre liste
$dateInter = trim((string) ($_POST['date_intervention'] ?? ''));
$heureInter = (string) ($_POST['heure_intervention'] ?? '');
if ($dateInter === '') {
    $dateInter = null;
} else {
    $d = DateTime::createFromFormat('!Y-m-d', $dateInter);
    // createFromFormat accepte aussi « 2026-02-31 » en le décalant : on compare au texte reçu pour le refuser
    if (!$d || $d->format('Y-m-d') !== $dateInter) {
        retour($id, 'erreur', "La date d'intervention n'est pas valide.");
    }
    if ($d > new DateTime('+1 year')) {
        retour($id, 'erreur', "La date d'intervention doit être dans l'année qui vient.");
    }
}
if ($heureInter !== '' && !in_array($heureInter, CRENEAUX, true)) {
    retour($id, 'erreur', 'Créneau inconnu.');
}
$heureInter = $heureInter === '' ? null : $heureInter;

// Une date sur un devis accepté = intervention planifiée (évite d'oublier de changer le statut)
if ($dateInter !== null && $statut === 'accepte') {
    $statut = 'planifie';
}
if (in_array($statut, ['planifie', 'termine'], true) && $dateInter === null) {
    retour($id, 'erreur', "Indiquez la date de l'intervention pour ce statut.");
}

$stmt = $pdo->prepare('SELECT numero_suivi, prenom, email, fichier_devis, fichier_facture, date_intervention, heure_intervention FROM devis WHERE id = :id');
$stmt->execute([':id' => $id]);
$demande = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$demande) {
    retour(0, 'erreur', 'Demande introuvable.');
}

/**
 * Vérifie et range un PDF envoyé.
 * Renvoie : null si aucun fichier, le chemin à enregistrer si tout va bien,
 * ou lance une erreur lisible (retour à la fiche) si le fichier est refusé.
 */
function traiterUploadPdf(string $champ, string $sousDossier, string $numeroSuivi, int $id): ?string
{
    $f = $_FILES[$champ] ?? null;
    if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $nom = $sousDossier === 'devis' ? 'Le devis' : 'La facture';

    if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE || $f['size'] > 5 * 1024 * 1024) {
        retour($id, 'erreur', "$nom dépasse 5 Mo.");
    }
    if ($f['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) {
        retour($id, 'erreur', "$nom n'a pas pu être envoyé, réessayez.");
    }

    // On vérifie le VRAI contenu (signature du fichier), pas l'extension ni le type annoncé par le navigateur
    $typeReel = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if ($typeReel !== 'application/pdf') {
        retour($id, 'erreur', "$nom doit être un fichier PDF.");
    }

    $dossier = __DIR__ . '/../uploads/' . $sousDossier;
    if (!is_dir($dossier) && !mkdir($dossier, 0750, true)) {
        retour($id, 'erreur', 'Dossier de stockage introuvable sur le serveur.');
    }

    // Nom choisi par nous : numéro de suivi nettoyé (uniquement lettres, chiffres, tiret)
    $nomFichier = preg_replace('/[^A-Za-z0-9-]/', '', $numeroSuivi) . '.pdf';
    if (!move_uploaded_file($f['tmp_name'], $dossier . '/' . $nomFichier)) {
        retour($id, 'erreur', "$nom n'a pas pu être enregistré.");
    }
    return 'uploads/' . $sousDossier . '/' . $nomFichier;
}

$nouveauDevis   = traiterUploadPdf('fichier_devis', 'devis', $demande['numero_suivi'], $id);
$nouvelleFacture = traiterUploadPdf('fichier_facture', 'factures', $demande['numero_suivi'], $id);
$fichierDevis   = $nouveauDevis ?? $demande['fichier_devis'];
$fichierFacture = $nouvelleFacture ?? $demande['fichier_facture'];

$stmt = $pdo->prepare('UPDATE devis
                       SET statut = :statut, montant_devis = :montant,
                           fichier_devis = :fichier_devis, fichier_facture = :fichier_facture,
                           date_intervention = :date_inter, heure_intervention = :heure_inter
                       WHERE id = :id');
$stmt->execute([
    ':statut'          => $statut,
    ':montant'         => $montant,
    ':fichier_devis'   => $fichierDevis,
    ':fichier_facture' => $fichierFacture,
    ':date_inter'      => $dateInter,
    ':heure_inter'     => $heureInter,
    ':id'              => $id,
]);

// --- Mails au client --------------------------------------------------------
// On écrit seulement quand il y a du nouveau pour lui (pas pour un simple changement de statut).
$documents = [];
if ($nouveauDevis)    { $documents[] = 'devis'; }
if ($nouvelleFacture) { $documents[] = 'facture'; }

// Intervention : nouvelle date, ou date/créneau modifiés (et pas pour une intervention déjà réalisée)
$interChangee = $dateInter !== null && $statut === 'planifie'
    && ($dateInter !== $demande['date_intervention'] || $heureInter !== $demande['heure_intervention']);

if (!$documents && !$interChangee) {
    retour($id, 'succes', 'Demande mise à jour.');
}

require_once __DIR__ . '/../mailer.php';
$infos = [];
$echec = false;

if ($documents) {
    $ok = envoyerNotificationDocument($demande['email'], $demande['prenom'], $demande['numero_suivi'], $documents, $montant, lien_espace_client());
    if ($ok) {
        $infos[] = implode(' et ', $documents) . (count($documents) > 1 ? ' disponibles' : ' disponible');
    } else {
        $echec = true;
    }
}
if ($interChangee) {
    $ok = envoyerEmailIntervention($demande['email'], $demande['prenom'], $demande['numero_suivi'],
                                   $dateInter, (string) $heureInter, $demande['date_intervention'] !== null);
    if ($ok) {
        $infos[] = "date d'intervention";
    } else {
        $echec = true;
    }
}

if ($echec) {
    retour($id, 'erreur', "Demande mise à jour, mais un e-mail au client n'a pas pu partir. Prévenez-le par téléphone.");
}
retour($id, 'succes', 'Demande mise à jour. Le client a été prévenu par e-mail (' . implode(', ', $infos) . ').');