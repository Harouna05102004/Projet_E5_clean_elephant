<?php
/*
 * espace-client/traitement_verification.php — Vérifie le code à usage unique
 *
 * Protections ajoutées :
 *   - jeton CSRF ;
 *   - 5 essais maximum par code : au 5e échec, le code est annulé.
 *     Sans cette limite, un robot pourrait tester les 1 000 000 de codes
 *     possibles en quelques minutes (attaque par force brute) ;
 *   - comparaison avec hash_equals (temps constant) ;
 *   - session_regenerate_id après connexion (contre le vol de session par fixation).
 *
 * Nécessite la colonne otp_codes.tentatives (voir migration_v3_otp.sql).
 */
require_once __DIR__ . '/../includes/securite.php';
require_once __DIR__ . '/../config.php';

const ESSAIS_MAX = 5;

function retourCode(string $erreur): void
{
    $_SESSION['ec_erreur'] = $erreur;
    header('Location: verifier.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['espace_client_en_attente'])) {
    header('Location: index.php');
    exit;
}
if (!csrf_verifier()) {
    retourCode('Votre session a expiré. Merci de ressaisir le code.');
}

$devisId = (int) $_SESSION['espace_client_en_attente'];
$code    = lire_texte('code');

if (!preg_match('/^[0-9]{6}$/', $code)) {
    retourCode('Le code contient exactement 6 chiffres.');
}

// Le dernier code encore valable pour cette demande (non utilisé, non expiré)
$stmt = $pdo->prepare('SELECT id, code, tentatives FROM otp_codes
                       WHERE devis_id = :devis_id AND utilise = 0 AND expire_at >= NOW()
                       ORDER BY id DESC LIMIT 1');
$stmt->execute([':devis_id' => $devisId]);
$otp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$otp) {
    $_SESSION['ec_erreur'] = 'Ce code a expiré ou a déjà servi. Demandez-en un nouveau.';
    $_SESSION['ec_numero'] = $_SESSION['ec_numero_attente'] ?? '';
    unset($_SESSION['espace_client_en_attente']);
    header('Location: index.php');
    exit;
}

if (!hash_equals($otp['code'], $code)) {
    // Mauvais code : on compte l'essai, et on annule le code au 5e échec
    $essais = (int) $otp['tentatives'] + 1;
    $stmt = $pdo->prepare('UPDATE otp_codes SET tentatives = :essais, utilise = :bloque WHERE id = :id');
    $stmt->execute([
        ':essais' => $essais,
        ':bloque' => $essais >= ESSAIS_MAX ? 1 : 0,
        ':id'     => $otp['id'],
    ]);

    $restants = ESSAIS_MAX - $essais;
    if ($restants <= 0) {
        $_SESSION['ec_erreur'] = 'Trop d\'essais : ce code est désactivé. Demandez-en un nouveau.';
        $_SESSION['ec_numero'] = $_SESSION['ec_numero_attente'] ?? '';
        unset($_SESSION['espace_client_en_attente']);
        header('Location: index.php');
        exit;
    }
    retourCode('Code incorrect. Il vous reste ' . $restants . ' essai' . ($restants > 1 ? 's' : '') . '.');
}

// Code correct : il ne pourra plus resservir
$stmt = $pdo->prepare('UPDATE otp_codes SET utilise = 1 WHERE id = :id');
$stmt->execute([':id' => $otp['id']]);

// Nouvel identifiant de session au moment de la connexion (protection contre la fixation de session)
session_regenerate_id(true);
$_SESSION['espace_client_devis_id']       = $devisId;
$_SESSION['espace_client_derniere_action'] = time();
unset($_SESSION['espace_client_en_attente'], $_SESSION['ec_email_masque'], $_SESSION['ec_numero_attente']);

header('Location: espace.php');
exit;