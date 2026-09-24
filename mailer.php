<?php
// Configuration de l'envoi d'email pour Clean Elephant
// À adapter avec tes propres identifiants SMTP

require __DIR__ . '/lib/PHPMailer/Exception.php';
require __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envoyerEmailConfirmationDevis($destinataire, $prenom) {
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP (sandbox Mailtrap - environnement de test E5)
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Expéditeur / destinataire
        $mail->setFrom('contact@cleanelephant.fr', 'Clean Elephant');
        $mail->addAddress($destinataire);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Votre demande de devis - Clean Elephant';
        $mail->Body    = "
            <p>Bonjour $prenom,</p>
            <p>Nous avons bien reçu votre demande de devis. Notre équipe vous recontactera <strong>sous 24h</strong> avec une proposition adaptée à votre besoin.</p>
            <p>À très vite,<br>L'équipe Clean Elephant</p>
        ";
        $mail->AltBody = "Bonjour $prenom,\n\nNous avons bien reçu votre demande de devis. Notre équipe vous recontactera sous 24h.\n\nL'équipe Clean Elephant";

        $mail->send();
        return true;

    } catch (Exception $e) {
        // On log l'erreur sans bloquer le parcours utilisateur
        error_log("Erreur envoi email : " . $mail->ErrorInfo);
        return false;
    }
}
?>
