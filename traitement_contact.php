<?php
// Traitement du formulaire de contact
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message']));

    $stmt = $pdo->prepare("INSERT INTO contacts (prenom, nom, email, telephone, message) 
                            VALUES (:prenom, :nom, :email, :telephone, :message)");

    $stmt->execute([
        ':prenom' => $prenom,
        ':nom' => $nom,
        ':email' => $email,
        ':telephone' => $telephone,
        ':message' => $message
    ]);

    header("Location: contact.php?succes=1");
    exit;

} else {
    header("Location: contact.php");
    exit;
}
?>
