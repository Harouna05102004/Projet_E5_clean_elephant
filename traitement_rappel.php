<?php
// Traitement du formulaire "Nous vous rappelons gratuitement" (footer)
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));

    $stmt = $pdo->prepare("INSERT INTO rappels (nom, prenom, telephone) 
                            VALUES (:nom, :prenom, :telephone)");

    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':telephone' => $telephone
    ]);

    // Redirige vers la page d'où venait le visiteur (le formulaire est dans le footer, présent sur toutes les pages)
    $page_retour = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    header("Location: " . $page_retour . (strpos($page_retour, '?') === false ? '?' : '&') . "rappel=1");
    exit;

} else {
    header("Location: index.php");
    exit;
}
?>
