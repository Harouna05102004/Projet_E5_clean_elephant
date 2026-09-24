<?php
// Traitement du formulaire de devis
require 'config.php';
require 'mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération et nettoyage des champs
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $email = htmlspecialchars(trim($_POST['email']));
    $adresse = htmlspecialchars(trim($_POST['adresse'] ?? ''));
    $ville = htmlspecialchars(trim($_POST['ville'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    // Les cases cochées arrivent sous forme de tableau, on les regroupe en une seule chaîne
    $types = isset($_POST['type_nettoyage']) ? implode(', ', $_POST['type_nettoyage']) : '';

    // Insertion en base de données (requête préparée pour éviter les injections SQL)
    $stmt = $pdo->prepare("INSERT INTO devis (nom, prenom, telephone, email, adresse, ville, type_nettoyage, message) 
                            VALUES (:nom, :prenom, :telephone, :email, :adresse, :ville, :type_nettoyage, :message)");

    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':telephone' => $telephone,
        ':email' => $email,
        ':adresse' => $adresse,
        ':ville' => $ville,
        ':type_nettoyage' => $types,
        ':message' => $message
    ]);

    // Envoi de l'email de confirmation (service externe exigé par le cahier des charges)
    envoyerEmailConfirmationDevis($email, $prenom);

    // Redirection vers la page devis avec message de succès
    header("Location: devis.php?succes=1");
    exit;

} else {
    // Si on accède directement au fichier sans passer par le formulaire
    header("Location: devis.php");
    exit;
}
?>
