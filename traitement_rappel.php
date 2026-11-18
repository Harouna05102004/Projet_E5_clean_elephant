<?php
/*
 * traitement_rappel.php : Reçoit le formulaire « Nous vous rappelons gratuitement »
 * (présent dans le pied de page de toutes les pages).
 *
 * Mêmes protections que traitement_devis.php : POST uniquement, champ piège,
 * jeton CSRF, limite d'envoi, validation de chaque champ, requête préparée.
 * Le résultat est renvoyé à la page d'origine sous forme de message flash.
 */
require_once __DIR__ . '/includes/securite.php';
require_once __DIR__ . '/config.php';

/*
 * Page où renvoyer le visiteur. L'ancienne version renvoyait vers HTTP_REFERER
 * tel quel : cet en-tête est fourni par le navigateur, donc falsifiable
 * (risque de redirection vers un site extérieur). On n'accepte qu'un chemin
 * de NOTRE site ; sinon, retour à l'accueil.
 */
function urlRetour(): string
{
    $referer = parse_url($_SERVER['HTTP_REFERER'] ?? '');
    $hoteReferer = ($referer['host'] ?? '') . (isset($referer['port']) ? ':' . $referer['port'] : '');

    if ($hoteReferer !== '' && $hoteReferer === ($_SERVER['HTTP_HOST'] ?? '')
        && isset($referer['path']) && str_starts_with($referer['path'], '/')
        && !str_starts_with($referer['path'], '//')) {   // "//site.com" serait compris comme un autre site
        return $referer['path'] . '#rappel';
    }
    return 'index.php#rappel';
}

function retourRappel(string $type, string $texte): void
{
    $_SESSION['rappel_message'] = ['type' => $type, 'texte' => $texte];
    header('Location: ' . urlRetour());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (est_un_robot()) {
    header('Location: index.php');
    exit;
}

if (!csrf_verifier()) {
    retourRappel('erreur', 'Votre session a expiré. Merci de renvoyer le formulaire.');
}

if (isset($_SESSION['rappel_dernier_envoi']) && time() - $_SESSION['rappel_dernier_envoi'] < 60) {
    retourRappel('erreur', 'Une demande vient déjà d\'être envoyée. Merci de patienter une minute.');
}

$nom       = lire_texte('nom');
$prenom    = lire_texte('prenom');
$telephone = lire_texte('telephone');

$regexNom = "/^\p{L}[\p{L}\s'’\-]*$/u";
if ($nom === '' || mb_strlen($nom) > 100 || !preg_match($regexNom, $nom)
    || $prenom === '' || mb_strlen($prenom) > 100 || !preg_match($regexNom, $prenom)) {
    retourRappel('erreur', 'Nom ou prénom invalide (lettres, espaces, apostrophes et tirets uniquement).');
}
if (!preg_match('/^\+?[0-9 .\-]{10,20}$/', $telephone)) {
    retourRappel('erreur', 'Numéro de téléphone invalide.');
}

try {
    $stmt = $pdo->prepare('INSERT INTO rappels (nom, prenom, telephone) VALUES (:nom, :prenom, :telephone)');
    $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':telephone' => $telephone]);
} catch (PDOException $e) {
    error_log('Erreur insertion rappel : ' . $e->getMessage());
    retourRappel('erreur', 'Une erreur technique est survenue. Merci de nous appeler directement.');
}

$_SESSION['rappel_dernier_envoi'] = time();

// Le commercial est prévenu tout de suite. Si le mail échoue, la demande reste en base
// et apparaît dans l'admin (Rappels et messages) : rien n'est perdu.
require_once __DIR__ . '/mailer.php';
envoyerNotificationRappel(['nom' => $nom, 'prenom' => $prenom, 'telephone' => $telephone]);

retourRappel('succes', 'Merci ' . $prenom . ', nous vous rappelons très vite.');