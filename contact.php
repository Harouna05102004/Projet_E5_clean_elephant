<?php
/*
 * contact.php — Page contact
 * Coordonnées cliquables à gauche, formulaire à droite.
 * Même principe de sécurité que devis.php : jeton CSRF, champ piège,
 * messages flash en session, données réaffichées avec e().
 */
require_once __DIR__ . '/includes/securite.php';
require_once __DIR__ . '/includes/icones.php';

$page_active = 'contact';

$erreurs = $_SESSION['contact_erreurs'] ?? [];
$saisie  = $_SESSION['contact_saisie'] ?? [];
$succes  = !empty($_SESSION['contact_succes']);
unset($_SESSION['contact_erreurs'], $_SESSION['contact_saisie'], $_SESSION['contact_succes']);

function ancien(array $saisie, string $champ): string
{
    return e(is_string($saisie[$champ] ?? null) ? $saisie[$champ] : '');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | Clean Elephant</title>
    <meta name="description" content="Contactez Clean Elephant, nettoyage de vitres à Montmagny et en Île-de-France : téléphone, email ou formulaire. Du lundi au samedi, de 8h à 19h.">
    <meta name="theme-color" content="#0B1E3A">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main id="main-content">

    <section class="page-hero" aria-labelledby="titre-page">
        <div class="container">
            <nav class="breadcrumb" aria-label="Fil d'Ariane">
                <ol>
                    <li><a href="index.php">Accueil</a></li>
                    <li aria-current="page">Contact</li>
                </ol>
            </nav>
            <div class="page-hero__grid">
                <div>
                    <p class="kicker">Contact</p>
                    <h1 class="page-hero__title" id="titre-page">
                        <span class="line"><span>Une question ?</span></span>
                        <span class="line"><span><em>On vous répond.</em></span></span>
                    </h1>
                </div>
                <p class="lead">Par téléphone, par email ou avec le formulaire : notre équipe vous répond rapidement. Le devis reste 100 % gratuit et sans engagement.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container contact__grid">

            <div>
                <ul class="contact-cards">
                    <li>
                        <a class="contact-card" href="tel:+33749187647">
                            <span class="contact-card__icon"><?= icone('telephone') ?></span>
                            <span><small>Téléphone</small><strong>07 49 18 76 47</strong></span>
                        </a>
                    </li>
                    <li>
                        <a class="contact-card" href="mailto:contact@cleanelephant.fr">
                            <span class="contact-card__icon"><?= icone('email') ?></span>
                            <span><small>Email</small><strong>contact@cleanelephant.fr</strong></span>
                        </a>
                    </li>
                    <li>
                        <div class="contact-card">
                            <span class="contact-card__icon"><?= icone('horloge') ?></span>
                            <span><small>Horaires</small><strong>Du lundi au samedi, de 8h à 19h</strong></span>
                        </div>
                    </li>
                    <li>
                        <a class="contact-card" href="#zone">
                            <span class="contact-card__icon"><?= icone('repere') ?></span>
                            <span><small>Zone d'intervention</small><strong>Montmagny &amp; Île-de-France</strong></span>
                        </a>
                    </li>
                </ul>

                <!-- Remplace les # par les vraies adresses des pages -->
                <div class="socials socials--dark">
                    <a href="#" aria-label="Clean Elephant sur Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="#" aria-label="Clean Elephant sur LinkedIn">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9.5h4V21H3zM9.5 9.5h3.8v1.6h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.2c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V21h-4z"/></svg>
                    </a>
                    <a href="#" aria-label="Clean Elephant sur Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 8.5V6.8c0-.8.2-1.3 1.4-1.3H17V2.2C16.7 2.1 15.6 2 14.4 2 11.8 2 10 3.6 10 6.5v2H7.3v3.7H10V22h4v-9.8h2.8l.4-3.7z"/></svg>
                    </a>
                </div>
            </div>

            <div class="form-card" id="formulaire">
                <h2>Écrivez-nous</h2>

                <?php if ($succes): ?>
                    <div class="alert-success" role="status">Votre message a bien été envoyé, nous vous répondrons rapidement.</div>
                <?php endif; ?>

                <?php if ($erreurs): ?>
                    <div class="alert-error" role="alert">
                        <strong>Merci de corriger les points suivants :</strong>
                        <ul>
                            <?php foreach ($erreurs as $erreur): ?>
                                <li><?= e($erreur) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="traitement_contact.php" method="POST">
                    <?= csrf_champ() ?>
                    <?= honeypot_champ() ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" maxlength="100" autocomplete="given-name"
                                   pattern="[^0-9]*" title="Le prénom ne doit pas contenir de chiffres" required
                                   value="<?= ancien($saisie, 'prenom') ?>">
                        </div>
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" maxlength="100" autocomplete="family-name"
                                   pattern="[^0-9]*" title="Le nom ne doit pas contenir de chiffres" required
                                   value="<?= ancien($saisie, 'nom') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" maxlength="150" autocomplete="email" required
                                   value="<?= ancien($saisie, 'email') ?>">
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" maxlength="20" autocomplete="tel"
                                   value="<?= ancien($saisie, 'telephone') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" maxlength="2000" required><?= ancien($saisie, 'message') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Envoyer mon message <?= icone('fleche') ?></button>

                    <p class="form-note rgpd-note">
                        Vos informations servent uniquement à répondre à votre message et ne sont jamais revendues.
                        Vous pouvez demander à y accéder ou à les supprimer en écrivant à
                        <a href="mailto:contact@cleanelephant.fr">contact@cleanelephant.fr</a>.
                        Voir notre <a href="confidentialite.php">politique de confidentialité</a>.
                    </p>
                </form>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>