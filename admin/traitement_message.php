<?php
/*
 * admin/traitement_message.php : marque une demande de rappel ou un message « traité »
 * (ou le remet « à traiter »).
 * Protections : admin connecté (auth.php), POST + jeton CSRF, type et action pris
 * dans des listes fermées, identifiant converti en nombre, requête préparée.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

$type   = $_POST['type'] ?? '';
$action = $_POST['action'] ?? '';
$id     = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$tables = ['rappels' => 'rappels', 'contacts' => 'contacts'];
$retour = 'messages.php?onglet=' . (isset($tables[$type]) ? $type : 'rappels');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verifier()
    || !isset($tables[$type]) || !in_array($action, ['traiter', 'rouvrir'], true) || !$id) {
    $_SESSION['admin_flash'] = ['type' => 'erreur', 'message' => 'Action impossible. Rechargez la page puis recommencez.'];
    header('Location: ' . $retour);
    exit;
}

$table = $tables[$type];   // vient de notre liste : sûr dans la requête
$valeur = $action === 'traiter' ? 'NOW()' : 'NULL';
$stmt = $pdo->prepare("UPDATE $table SET traite_le = $valeur WHERE id = :id");
$stmt->execute([':id' => $id]);

$_SESSION['admin_flash'] = ['type' => 'succes', 'message' => $action === 'traiter' ? 'Marqué comme traité.' : 'Remis dans « À traiter ».'];
header('Location: ' . $retour . ($action === 'rouvrir' ? '&vue=traites' : ''));
exit;