<?php
/*
 * espace-client/index.php — Étape 1 : saisie du numéro de suivi
 *
 * Parcours de connexion (sans mot de passe) :
 *   1. le client saisit son numéro de suivi (cette page) ;
 *   2. il reçoit un code à 6 chiffres par email (verifier.php) ;
 *   3. il accède au suivi de SA demande (espace.php).
 *
 * Changements : nouvel en-tête / pied de page du site, jeton CSRF, champ piège,
 * messages d'erreur transmis par la session (et plus par l'URL).
 */
require_once __DIR__ . '/../includes/securite.php';   // en premier : session sécurisée
require_once __DIR__ . '/../includes/icones.php';

$base        = '../';      // on est dans un sous-dossier : les liens du site remontent d'un niveau
$page_active = 'suivi';

// Déjà connecté ? On l'envoie directement sur son suivi
if (!empty($_SESSION['espace_client_devis_id'])) {
    header('Location: espace.php');
    exit;
}

// Messages flash
$erreur      = $_SESSION['ec_erreur'] ?? '';
$numeroSaisi = $_SESSION['ec_numero'] ?? '';
unset($_SESSION['ec_erreur'], $_SESSION['ec_numero']);
$deconnecte  = isset($_GET['deconnecte']);   // simple drapeau : aucun texte de l'URL n'est affiché
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de ma demande | Clean Elephant</title>
    <meta name="robots" content="noindex">   <!-- page de connexion : inutile dans Google -->
    <meta name="theme-color" content="#0B1E3A">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main id="main-content">
    <section class="section auth">
        <div class="container auth__grid">

            <div>
                <p class="kicker">Espace client</p>
                <h1 class="page-hero__title">
                    <span class="line"><span>Suivez votre</span></span>
                    <span class="line"><span><em>demande.</em></span></span>
                </h1>
                <p class="lead">Statut de votre rendez-vous, devis et facture : tout est ici, sans créer de compte ni retenir de mot de passe.</p>

                <!-- Les 3 étapes de la connexion : le visiteur sait où il en est -->
                <ol class="stepper" aria-label="Étapes de connexion">
                    <li class="is-current" aria-current="step"><span>1</span> Numéro de suivi</li>
                    <li><span>2</span> Code reçu par email</li>
                    <li><span>3</span> Mon suivi</li>
                </ol>
            </div>

            <div class="form-card">
                <h2>Votre numéro de suivi</h2>

                <?php if ($deconnecte): ?>
                    <div class="alert-success" role="status">Vous êtes bien déconnecté. À bientôt !</div>
                <?php endif; ?>

                <?php if ($erreur): ?>
                    <div class="alert-error" role="alert"><?= e($erreur) ?></div>
                <?php endif; ?>

                <form action="traitement_demande_code.php" method="POST">
                    <?= csrf_champ() ?>
                    <?= honeypot_champ() ?>

                    <div class="form-group">
                        <label for="numero_suivi">Numéro de suivi</label>
                        <!-- autocapitalize : le clavier du téléphone écrit directement en majuscules -->
                        <input type="text" id="numero_suivi" name="numero_suivi" class="input-code"
                               placeholder="CE-XXXXXXXX" maxlength="20" autocomplete="off" autocapitalize="characters"
                               spellcheck="false" required autofocus value="<?= e($numeroSaisi) ?>">
                        <p class="form-note form-note-inline">Il figure dans l'email « Votre demande de rendez-vous » que vous avez reçu.</p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Recevoir mon code de connexion <?= icone('fleche') ?></button>
                </form>

                <p class="form-note">Pas encore de demande ? <a href="../devis.php">Demandez votre devis gratuit</a>.</p>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>