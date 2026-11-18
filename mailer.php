<?php
/*
 * mailer.php : Envoi des emails de Clean Elephant (PHPMailer)
 *
 * Changements par rapport à l'ancienne version :
 *   - creerMailer() regroupe la configuration SMTP qui était copiée-collée
 *     dans chaque fonction (un seul endroit à modifier) ;
 *   - toutes les données du client sont échappées avec e() dans le HTML du mail,
 *     puisqu'elles sont désormais stockées brutes ;
 *   - nouvelle fonction envoyerNotificationCommercial() : le cahier des charges
 *     exige de transmettre automatiquement chaque nouvelle demande au commercial.
 */
require_once __DIR__ . '/lib/PHPMailer/Exception.php';
require_once __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/includes/securite.php';   // pour e()

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Prépare un objet PHPMailer déjà configuré (serveur SMTP + expéditeur)
function creerMailer(): PHPMailer
{
    $mail = new PHPMailer(true);           // true = les erreurs lèvent des exceptions
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // connexion chiffrée vers le serveur SMTP
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom('contact@cleanelephant.fr', 'Clean Elephant');
    return $mail;
}

function formaterDate(string $dateRdv): string
{
    return $dateRdv ? date('d/m/Y', strtotime($dateRdv)) : '';
}

// Mail de confirmation envoyé au client
function envoyerEmailConfirmationDevis($destinataire, $prenom, $numeroSuivi, $dateRdv, $heureRdv): bool
{
    try {
        $mail = creerMailer();
        $mail->addAddress($destinataire);

        $date = formaterDate($dateRdv);

        $mail->isHTML(true);
        $mail->Subject = 'Votre demande de rendez-vous - Clean Elephant';
        $mail->Body = '
            <p>Bonjour ' . e($prenom) . ',</p>
            <p>Nous avons bien reçu votre demande de rendez-vous pour l\'établissement de votre devis, le <strong>'
                . e($date) . ' (' . e($heureRdv) . ')</strong>. Notre équipe vous confirmera ce créneau sous 24h.</p>
            <p>Votre numéro de suivi est : <strong style="font-size:1.2em;">' . e($numeroSuivi) . '</strong></p>
            <p>Il vous permettra de suivre l\'avancement de votre demande et de consulter votre devis puis votre facture, dans votre espace client.</p>
            <p>À très vite,<br>L\'équipe Clean Elephant</p>';
        // Version texte (pas de HTML -> pas besoin d'échapper)
        $mail->AltBody = "Bonjour $prenom,\n\nNous avons bien reçu votre demande de rendez-vous pour l'établissement de votre devis, le $date ($heureRdv).\n\nVotre numéro de suivi est : $numeroSuivi\n\nL'équipe Clean Elephant";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi email confirmation : ' . $e->getMessage());
        return false;
    }
}

// Notification interne envoyée au service commercial à chaque nouvelle demande
function envoyerNotificationCommercial(array $d): bool
{
    try {
        $mail = creerMailer();
        $mail->addAddress(COMMERCIAL_EMAIL);
        // "Répondre" dans la messagerie du commercial écrira directement au client
        $mail->addReplyTo($d['email'], $d['prenom'] . ' ' . $d['nom']);

        $lignes = [
            'N° de suivi'  => $d['numero_suivi'],
            'Client'       => $d['prenom'] . ' ' . $d['nom'],
            'Téléphone'    => $d['telephone'],
            'Email'        => $d['email'],
            'Adresse'      => trim($d['adresse'] . ' ' . $d['ville']),
            'Prestations'  => $d['types'],
            'RDV souhaité' => formaterDate($d['date_rdv']) . ' (' . $d['heure_rdv'] . ')',
            'Message'      => $d['message'],
        ];

        $html  = '<p>Nouvelle demande de rendez-vous reçue depuis le site :</p><table cellpadding="6" border="1" style="border-collapse:collapse;">';
        $texte = "Nouvelle demande de rendez-vous reçue depuis le site :\n\n";
        foreach ($lignes as $libelle => $valeur) {
            // nl2br garde les retours à la ligne du message, APRÈS l'échappement
            $html  .= '<tr><th align="left">' . e($libelle) . '</th><td>' . nl2br(e($valeur)) . '</td></tr>';
            $texte .= "$libelle : $valeur\n";
        }
        $html .= '</table><p>À traiter depuis l\'espace admin.</p>';

        $mail->isHTML(true);
        $mail->Subject = 'Nouvelle demande de RDV ' . $d['numero_suivi'];
        $mail->Body    = $html;
        $mail->AltBody = $texte;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi notification commercial : ' . $e->getMessage());
        return false;
    }
}

// Code de connexion à usage unique pour l'espace client (inchangé, sauf l'échappement)
function envoyerEmailCodeOTP($destinataire, $prenom, $code): bool
{
    try {
        $mail = creerMailer();
        $mail->addAddress($destinataire);

        $mail->isHTML(true);
        $mail->Subject = 'Votre code de connexion - Clean Elephant';
        $mail->Body = '
            <p>Bonjour ' . e($prenom) . ',</p>
            <p>Voici votre code de connexion à usage unique à l\'espace client Clean Elephant :</p>
            <p style="font-size:1.6em; font-weight:bold; letter-spacing:4px;">' . e($code) . '</p>
            <p>Ce code est valable 10 minutes. Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet email.</p>
            <p>L\'équipe Clean Elephant</p>';
        $mail->AltBody = "Bonjour $prenom,\n\nVotre code de connexion à usage unique : $code\n\nCe code est valable 10 minutes.\n\nL'équipe Clean Elephant";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi email (code OTP) : ' . $e->getMessage());
        return false;
    }
}

// Prévient le client qu'un document (devis et/ou facture) est disponible dans son espace.
// Le PDF n'est PAS joint au mail : un e-mail peut être transféré, intercepté ou lu par
// quelqu'un d'autre. Le client le télécharge depuis son espace, après s'être connecté.
function envoyerNotificationDocument($destinataire, $prenom, $numeroSuivi, array $documents, $montant, string $lienEspace): bool
{
    try {
        $mail = creerMailer();
        $mail->addAddress($destinataire);

        // « votre devis », « votre facture » ou « votre devis et votre facture »
        $liste  = implode(' et ', array_map(fn($d) => 'votre ' . $d, $documents));
        $pluriel = count($documents) > 1;
        $ligneMontant = ($montant !== null && in_array('devis', $documents, true))
            ? 'Montant du devis : ' . number_format((float) $montant, 2, ',', ' ') . ' € TTC.'
            : '';

        $mail->isHTML(true);
        $mail->Subject = ucfirst($liste) . ($pluriel ? ' sont disponibles' : ' est disponible') . ' - ' . $numeroSuivi;
        $mail->Body = '
            <p>Bonjour ' . e($prenom) . ',</p>
            <p>' . e(ucfirst($liste)) . ' pour la demande <strong>' . e($numeroSuivi) . '</strong> '
                . ($pluriel ? 'sont disponibles' : 'est disponible') . ' dans votre espace de suivi.</p>'
            . ($ligneMontant ? '<p>' . e($ligneMontant) . '</p>' : '') . '
            <p><a href="' . e($lienEspace) . '" style="display:inline-block;padding:12px 22px;background:#F5B400;color:#0B1E3A;font-weight:bold;text-decoration:none;border-radius:6px;">Accéder à mon espace</a></p>
            <p>Pour vous connecter, il vous suffit de votre numéro de suivi : nous vous enverrons un code à usage unique par e-mail.</p>
            <p>Une question ? Répondez simplement à cet e-mail ou appelez-nous au 07 49 18 76 47.</p>
            <p>L\'équipe Clean Elephant</p>';
        $mail->AltBody = "Bonjour $prenom,\n\n" . ucfirst($liste) . " pour la demande $numeroSuivi "
            . ($pluriel ? 'sont disponibles' : 'est disponible') . " dans votre espace de suivi.\n"
            . ($ligneMontant ? "$ligneMontant\n" : '')
            . "\nAccéder à mon espace : $lienEspace\n\nL'équipe Clean Elephant";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi notification document : ' . $e->getMessage());
        return false;
    }
}

// Adresse de l'espace client, pour les liens dans les mails.
// En production : SITE_URL dans env.php (ex. https://www.cleanelephant.fr/).
// En local : déduite de l'adresse actuelle. Le script appelant est toujours dans
// un sous-dossier (admin/ ou espace-client/), donc on remonte de deux niveaux.
function lien_espace_client(): string
{
    if (defined('SITE_URL')) {
        return rtrim(SITE_URL, '/') . '/espace-client/';
    }
    $schema = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
    $racine = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
    return $schema . '://' . $_SERVER['HTTP_HOST'] . $racine . '/espace-client/';
}

// Prévient le commercial que le client a accepté ou refusé le devis en ligne
function envoyerNotificationReponse(array $d, string $decision, ?string $motif): bool
{
    try {
        $mail = creerMailer();
        $mail->addAddress(COMMERCIAL_EMAIL);
        $mail->addReplyTo($d['email'], $d['prenom'] . ' ' . $d['nom']);

        $accepte = $decision === 'accepte';
        $montant = $d['montant_devis'] !== null ? number_format((float) $d['montant_devis'], 2, ',', ' ') . ' €' : 'montant non renseigné';
        $client  = $d['prenom'] . ' ' . $d['nom'];

        $mail->isHTML(true);
        $mail->Subject = ($accepte ? 'Devis ACCEPTÉ' : 'Devis refusé') . ' - ' . $d['numero_suivi'];
        $mail->Body = '
            <p><strong>' . e($client) . '</strong> a <strong>' . ($accepte ? 'accepté' : 'refusé') . '</strong> en ligne le devis '
                . e($d['numero_suivi']) . ' (' . e($montant) . '), le ' . date('d/m/Y à H:i') . '.</p>'
            . (!$accepte && $motif ? '<p>Raison indiquée : « ' . nl2br(e($motif)) . ' »</p>' : '')
            . '<p>Téléphone : ' . e($d['telephone']) . '</p>'
            . ($accepte ? '<p>Prochaine étape : fixer la date d\'intervention dans l\'espace admin (le client sera prévenu par e-mail).</p>' : '');
        $mail->AltBody = "$client a " . ($accepte ? 'accepté' : 'refusé') . " en ligne le devis {$d['numero_suivi']} ($montant)."
            . (!$accepte && $motif ? "\nRaison : $motif" : '') . "\nTéléphone : {$d['telephone']}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi notification réponse : ' . $e->getMessage());
        return false;
    }
}

// Prévient le client de la date de son intervention (ou de son changement)
function envoyerEmailIntervention($destinataire, $prenom, $numeroSuivi, string $date, string $heure, bool $modification): bool
{
    try {
        $mail = creerMailer();
        $mail->addAddress($destinataire);
        $quand = formaterDate($date) . ($heure ? ', ' . $heure : '');
        $lien  = lien_espace_client();

        $mail->isHTML(true);
        $mail->Subject = ($modification ? 'Nouvelle date pour votre intervention' : 'Votre intervention est planifiée') . ' - ' . $numeroSuivi;
        $mail->Body = '
            <p>Bonjour ' . e($prenom) . ',</p>
            <p>' . ($modification ? 'La date de votre intervention a changé. Elle aura lieu le' : 'Bonne nouvelle : votre intervention est planifiée le')
                . ' <strong>' . e($quand) . '</strong>.</p>
            <p>Merci de vous assurer que nos techniciens pourront accéder aux vitres (fenêtres dégagées, accès au bâtiment).</p>
            <p><a href="' . e($lien) . '" style="display:inline-block;padding:12px 22px;background:#F5B400;color:#0B1E3A;font-weight:bold;text-decoration:none;border-radius:6px;">Voir mon suivi</a></p>
            <p>Un empêchement ? Appelez-nous au 07 49 18 76 47.</p>
            <p>L\'équipe Clean Elephant</p>';
        $mail->AltBody = "Bonjour $prenom,\n\nVotre intervention ($numeroSuivi) "
            . ($modification ? 'a été déplacée au' : 'est planifiée le') . " $quand.\n\nVoir mon suivi : $lien\n\nUn empêchement ? 07 49 18 76 47\n\nL'équipe Clean Elephant";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi email intervention : ' . $e->getMessage());
        return false;
    }
}

// Prévient le commercial qu'un visiteur demande à être rappelé (formulaire du pied de page)
function envoyerNotificationRappel(array $r): bool
{
    try {
        $mail = creerMailer();
        $mail->addAddress(COMMERCIAL_EMAIL);
        $client = $r['prenom'] . ' ' . $r['nom'];
        $tel = preg_replace('/[^0-9+]/', '', $r['telephone']);   // pour le lien « appeler » sur téléphone

        $mail->isHTML(true);
        $mail->Subject = 'À rappeler : ' . $client;
        $mail->Body = '
            <p><strong>' . e($client) . '</strong> demande à être rappelé(e).</p>
            <p style="font-size:1.3em;"><a href="tel:' . e($tel) . '">' . e($r['telephone']) . '</a></p>
            <p>Demande reçue le ' . date('d/m/Y à H:i') . '. Pensez à la marquer « traitée » dans l\'espace admin (Rappels et messages).</p>';
        $mail->AltBody = "$client demande à être rappelé(e) : {$r['telephone']}\nReçue le " . date('d/m/Y à H:i');

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi notification rappel : ' . $e->getMessage());
        return false;
    }
}

// Transmet au commercial un message du formulaire de contact.
// « Répondre » dans sa messagerie écrira directement au visiteur.
function envoyerNotificationContact(array $c): bool
{
    try {
        $mail = creerMailer();
        $mail->addAddress(COMMERCIAL_EMAIL);
        $client = $c['prenom'] . ' ' . $c['nom'];
        $mail->addReplyTo($c['email'], $client);

        $mail->isHTML(true);
        $mail->Subject = 'Nouveau message de ' . $client;
        $mail->Body = '
            <p>Message reçu depuis le formulaire de contact, le ' . date('d/m/Y à H:i') . ' :</p>
            <blockquote style="margin:0;padding:12px 16px;border-left:4px solid #F5B400;background:#FDFBF7;">' . nl2br(e($c['message'])) . '</blockquote>
            <p><strong>' . e($client) . '</strong><br>' . e($c['email'])
                . ($c['telephone'] !== '' ? '<br>' . e($c['telephone']) : '') . '</p>
            <p>Répondez directement à cet e-mail pour lui écrire.</p>';
        $mail->AltBody = "Message de $client ({$c['email']}" . ($c['telephone'] !== '' ? ", {$c['telephone']}" : '') . ") :\n\n{$c['message']}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Erreur envoi notification contact : ' . $e->getMessage());
        return false;
    }
}