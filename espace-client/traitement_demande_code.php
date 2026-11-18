<?php
/*
 * espace-client/traitement_demande_code.php
 * Vérifie le numéro de suivi, crée un code à usage unique et l'envoie par email.
 *
 * Protections ajoutées :
 *   - jeton CSRF + champ piège (comme les autres formulaires) ;
 *   - format du numéro vérifié avant toute requête en base ;
 *   - 3 codes maximum par tranche de 15 minutes pour une même demande
 *     (évite de saturer la boîte mail du client ou notre quota d'envoi) ;
 *   - les anciens codes encore valides sont annulés : un seul code actif à la fois ;
 *   - code généré avec random_int (générateur cryptographique, imprévisible).
 */
require_once __DIR__ . '/../includes/securite.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../mailer.php';

function retourNumero(string $erreur, string $numero = ''): void
{
    $_SESSION['ec_erreur'] = $erreur;
    $_SESSION['ec_numero'] = $numero;
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || est_un_robot()) {
    header('Location: index.php');
    exit;
}
if (!csrf_verifier()) {
    retourNumero('Votre session a expiré. Merci de recommencer.');
}

// Mise en forme : majuscules, sans espaces (« ce-1a2b 3c4d » devient « CE-1A2B3C4D »)
$numero = strtoupper(str_replace(' ', '', lire_texte('numero_suivi')));

// Format attendu : CE- suivi de 8 lettres ou chiffres
if (!preg_match('/^CE-[A-Z0-9]{8}$/', $numero)) {
    retourNumero('Format attendu : CE- suivi de 8 caractères (exemple : CE-1A2B3C4D).', $numero);
}

$stmt = $pdo->prepare('SELECT id, prenom, email FROM devis WHERE numero_suivi = :numero');
$stmt->execute([':numero' => $numero]);
$demande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demande) {
    retourNumero('Numéro de suivi introuvable. Vérifiez l\'email de confirmation reçu.', $numero);
}

// Limite : 3 codes par 15 minutes pour cette demande
$stmt = $pdo->prepare('SELECT COUNT(*) FROM otp_codes
                       WHERE devis_id = :id AND date_creation >= NOW() - INTERVAL 15 MINUTE');
$stmt->execute([':id' => $demande['id']]);
if ((int) $stmt->fetchColumn() >= 3) {
    retourNumero('Trop de codes demandés. Patientez 15 minutes, ou utilisez le dernier code reçu.', $numero);
}

// Un seul code actif à la fois : on annule les précédents
$stmt = $pdo->prepare('UPDATE otp_codes SET utilise = 1 WHERE devis_id = :id AND utilise = 0');
$stmt->execute([':id' => $demande['id']]);

// Code à 6 chiffres (str_pad garde les zéros de tête : 004219 reste 004219)
$code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// L'expiration est calculée par MariaDB (NOW()), comme la vérification : même horloge, pas de décalage
$stmt = $pdo->prepare('INSERT INTO otp_codes (devis_id, code, expire_at)
                       VALUES (:devis_id, :code, NOW() + INTERVAL 10 MINUTE)');
$stmt->execute([':devis_id' => $demande['id'], ':code' => $code]);

envoyerEmailCodeOTP($demande['email'], $demande['prenom'], $code);

// Adresse masquée (h*****@gmail.com) : le client sait où regarder, sans dévoiler l'adresse complète
[$local, $domaine] = explode('@', $demande['email'], 2) + [1 => ''];
$_SESSION['ec_email_masque'] = mb_substr($local, 0, 1) . str_repeat('*', max(mb_strlen($local) - 1, 3)) . '@' . $domaine;

// En attente de vérification : le client n'est PAS encore connecté
$_SESSION['espace_client_en_attente'] = (int) $demande['id'];
$_SESSION['ec_numero_attente'] = $numero;

header('Location: verifier.php');
exit;