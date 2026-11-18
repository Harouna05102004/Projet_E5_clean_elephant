<?php
/*
 * espace-client/verifier.php — Étape 2 : saisie du code reçu par email
 */
require_once __DIR__ . '/../includes/securite.php';
require_once __DIR__ . '/../includes/icones.php';

$base        = '../';
$page_active = 'suivi';

// On n'arrive ici qu'après avoir donné un numéro de suivi valide
if (empty($_SESSION['espace_client_en_attente'])) {
    header('Location: index.php');
    exit;
}

$erreur      = $_SESSION['ec_erreur'] ?? '';
unset($_SESSION['ec_erreur']);
$emailMasque = $_SESSION['ec_email_masque'] ?? 'votre adresse email';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de connexion | Clean Elephant</title>
    <meta name="robots" content="noindex">
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
                    <span class="line"><span>Vérifiez vos</span></span>
                    <span class="line"><span><em>emails.</em></span></span>
                </h1>
                <p class="lead">Nous venons d'envoyer un code à 6 chiffres à <strong><?= e($emailMasque) ?></strong>. Il est valable 10 minutes. Pensez à regarder dans vos courriers indésirables.</p>

                <ol class="stepper" aria-label="Étapes de connexion">
                    <li class="is-done"><span><?= icone('check') ?></span> Numéro de suivi</li>
                    <li class="is-current" aria-current="step"><span>2</span> Code reçu par email</li>
                    <li><span>3</span> Mon suivi</li>
                </ol>
            </div>

            <div class="form-card">
                <h2>Code de connexion</h2>

                <?php if ($erreur): ?>
                    <div class="alert-error" role="alert"><?= e($erreur) ?></div>
                <?php endif; ?>

                <form action="traitement_verification.php" method="POST">
                    <?= csrf_champ() ?>

                    <div class="form-group">
                        <label for="code">Code reçu par email</label>
                        <!-- inputmode="numeric" : clavier chiffres sur mobile.
                             autocomplete="one-time-code" : le téléphone propose le code tout seul. -->
                        <input type="text" id="code" name="code" class="input-code input-code--otp"
                               inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6"
                               placeholder="000000" required autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Accéder à mon suivi <?= icone('fleche') ?></button>
                </form>

                <p class="form-note">Rien reçu ? <a href="index.php">Redemander un code</a>.</p>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>