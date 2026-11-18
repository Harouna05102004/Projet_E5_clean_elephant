<?php
/*
 * devis.php — Formulaire de demande de rendez-vous pour devis
 *
 * La logique est la même qu'à l'étape sécurité :
 *   - securite.php inclus en premier (session + jeton CSRF + e()) ;
 *   - erreurs, saisie précédente et numéro de suivi lus depuis la session (messages flash) ;
 *   - cases à cocher et créneaux générés depuis referentiels.php (une seule liste) ;
 *   - jeton CSRF, champ piège anti-robots, consentement et mention RGPD.
 * Nouveautés visuelles : formulaire découpé en 3 étapes (fieldset), créneaux en
 * « puces » cliquables (boutons radio), colonne d'informations collante à gauche.
 */
require_once __DIR__ . '/includes/securite.php';
require_once __DIR__ . '/includes/referentiels.php';
require_once __DIR__ . '/includes/icones.php';

$page_active = 'devis';

// Messages flash : on les lit puis on les efface, pour qu'ils ne s'affichent qu'une fois
$erreurs     = $_SESSION['devis_erreurs'] ?? [];
$saisie      = $_SESSION['devis_saisie'] ?? [];
$numeroSuivi = $_SESSION['devis_numero'] ?? '';
unset($_SESSION['devis_erreurs'], $_SESSION['devis_saisie'], $_SESSION['devis_numero']);

// Valeur à réafficher dans un champ après une erreur (vide sinon)
function ancien(array $saisie, string $champ): string
{
    return e(is_string($saisie[$champ] ?? null) ? $saisie[$champ] : '');
}

// Cases cochées : celles de la saisie précédente, sinon celle venant du lien (devis.php?type=maison)
$typesCoches = $saisie['types'] ?? [];
if (!$typesCoches && isset($_GET['type']) && is_string($_GET['type'])) {
    $typesCoches = [$_GET['type']];
}

// Profil pré-sélectionné : déduit de la prestation cochée (ex. arrivée depuis une tuile « Bureaux »)
$profilChoisi = '';
foreach ($typesCoches as $cle) {
    $profil = PROFIL_PAR_TYPE[$cle] ?? 'tous';
    if ($profil !== 'tous') {
        $profilChoisi = $profil;
        break;
    }
}

// Bornes du calendrier : de demain à +3 mois (rappel visuel ; la vraie vérification est côté serveur)
$dateMin = date('Y-m-d', strtotime('+1 day'));
$dateMax = date('Y-m-d', strtotime('+' . DELAI_MAX_RDV_JOURS . ' days'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis gratuit : prenez rendez-vous | Clean Elephant</title>
    <meta name="description" content="Demandez votre devis gratuit de nettoyage de vitres : choisissez une date, un technicien se déplace et vous remet un devis clair sous 24h.">
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
                    <li aria-current="page">Devis gratuit</li>
                </ol>
            </nav>
            <div class="page-hero__grid">
                <div>
                    <p class="kicker">Devis gratuit · Sans engagement</p>
                    <h1 class="page-hero__title" id="titre-page">
                        <span class="line"><span>Prenez rendez&#8209;vous</span></span>
                        <span class="line"><span><em>pour votre devis.</em></span></span>
                    </h1>
                </div>
                <p class="lead">Un technicien se déplace, évalue votre besoin et vous remet un devis clair et sans surprise. Comptez 2 minutes pour remplir le formulaire.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container devis__grid">

            <!-- Colonne d'informations (collante sur grand écran) -->
            <aside class="devis__aside" aria-label="Déroulement de votre demande">
                <div class="aside-card">
                    <h2>Et ensuite ?</h2>
                    <ol class="aside-steps">
                        <li><strong>Confirmation immédiate</strong>Vous recevez un numéro de suivi par email.</li>
                        <li><strong>Créneau confirmé sous 24h</strong>Notre équipe valide ou ajuste votre rendez-vous.</li>
                        <li><strong>Visite et devis</strong>Le technicien évalue votre besoin et vous remet le devis.</li>
                        <li><strong>Suivi en ligne</strong>Devis et facture disponibles dans votre espace client.</li>
                    </ol>
                </div>
                <a class="aside-phone" href="tel:+33749187647">
                    <?= icone('telephone') ?>
                    <span><small>Vous préférez appeler ?</small><strong>07 49 18 76 47</strong></span>
                </a>
            </aside>

            <div class="form-card" id="formulaire">
                <?php if ($numeroSuivi): ?>
                    <div class="alert-success" role="status">
                        Votre demande de rendez-vous a bien été envoyée. Nous vous confirmons le créneau sous 24h.
                        <br>Votre numéro de suivi : <strong><?= e($numeroSuivi) ?></strong> (il vous a aussi été envoyé par email).
                        Conservez-le pour suivre votre demande dans votre <a href="espace-client/index.php">espace client</a>.
                    </div>
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

                <form action="traitement_devis.php" method="POST">
                    <?= csrf_champ() ?>
                    <?= honeypot_champ() ?>

                    <!-- fieldset + legend : regroupe les champs liés ; le lecteur d'écran annonce le titre du groupe -->
                    <fieldset>
                        <legend><span aria-hidden="true">1</span> Vos coordonnées</legend>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" maxlength="100" autocomplete="family-name"
                                       pattern="[^0-9]*" title="Le nom ne doit pas contenir de chiffres" required
                                       value="<?= ancien($saisie, 'nom') ?>">
                            </div>
                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" maxlength="100" autocomplete="given-name"
                                       pattern="[^0-9]*" title="Le prénom ne doit pas contenir de chiffres" required
                                       value="<?= ancien($saisie, 'prenom') ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telephone">Téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" maxlength="20" autocomplete="tel" required
                                       value="<?= ancien($saisie, 'telephone') ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" maxlength="150" autocomplete="email" required
                                       value="<?= ancien($saisie, 'email') ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="adresse">Adresse</label>
                                <input type="text" id="adresse" name="adresse" maxlength="255" autocomplete="street-address"
                                       value="<?= ancien($saisie, 'adresse') ?>">
                            </div>
                            <div class="form-group">
                                <label for="ville">Ville</label>
                                <input type="text" id="ville" name="ville" maxlength="100" autocomplete="address-level2"
                                       value="<?= ancien($saisie, 'ville') ?>">
                            </div>
                        </div>
                    </fieldset>

                    <!-- data-filtre-profil : js/main.js n'affiche que les prestations du profil choisi.
                         Le champ « profil » n'est qu'une aide à la saisie : le serveur ne s'en sert pas. -->
                    <fieldset data-filtre-profil>
                        <legend><span aria-hidden="true">2</span> Votre besoin</legend>
                        <div class="form-group">
                            <span class="field-label" id="label-profil">Vous êtes</span>
                            <div class="chip-group" role="radiogroup" aria-labelledby="label-profil">
                                <label class="chip">
                                    <input type="radio" name="profil" value="particulier" <?= $profilChoisi === 'particulier' ? 'checked' : '' ?>>
                                    <span>Un particulier</span>
                                </label>
                                <label class="chip">
                                    <input type="radio" name="profil" value="pro" <?= $profilChoisi === 'pro' ? 'checked' : '' ?>>
                                    <span>Un professionnel</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <span class="field-label" id="label-types">Type de nettoyage</span>
                            <div class="checkbox-grid" role="group" aria-labelledby="label-types">
                                <?php foreach (TYPES_NETTOYAGE as $cle => $libelle): ?>
                                    <label class="checkbox-item" data-profil="<?= e(PROFIL_PAR_TYPE[$cle] ?? 'tous') ?>">
                                        <input type="checkbox" name="type_nettoyage[]" value="<?= e($cle) ?>"
                                            <?= in_array($cle, $typesCoches, true) ? 'checked' : '' ?>>
                                        <?= e($libelle) ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Message (optionnel)</label>
                            <textarea id="message" name="message" rows="4" maxlength="2000" placeholder="Nombre de fenêtres, étage, accès..."><?= ancien($saisie, 'message') ?></textarea>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend><span aria-hidden="true">3</span> Votre rendez-vous</legend>
                        <div class="form-group">
                            <label for="date_rdv">Date souhaitée * <small>(du lundi au samedi)</small></label>
                            <input type="date" id="date_rdv" name="date_rdv" min="<?= e($dateMin) ?>" max="<?= e($dateMax) ?>" required
                                   value="<?= ancien($saisie, 'date_rdv') ?>">
                        </div>
                        <div class="form-group">
                            <span class="field-label" id="label-creneau">Créneau souhaité *</span>
                            <!-- Boutons radio (un seul choix possible), même nom heure_rdv qu'avant :
                                 traitement_devis.php n'a rien à changer. -->
                            <div class="chip-group" role="radiogroup" aria-labelledby="label-creneau">
                                <?php foreach (CRENEAUX as $rang => $creneau): ?>
                                    <label class="chip">
                                        <input type="radio" name="heure_rdv" value="<?= e($creneau) ?>"
                                            <?= $rang === 0 ? 'required' : '' ?>
                                            <?= ($saisie['heure_rdv'] ?? '') === $creneau ? 'checked' : '' ?>>
                                        <span><?= e($creneau) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <p class="form-note form-note-inline">Ce créneau est une préférence : nous vous le confirmons (ou l'ajustons) sous 24h.</p>
                        </div>
                    </fieldset>

                    <div class="form-group">
                        <label class="checkbox-item">
                            <input type="checkbox" name="rgpd" value="1" required <?= !empty($saisie['rgpd']) ? 'checked' : '' ?>>
                            J'accepte que mes données soient utilisées par Clean Elephant pour traiter ma demande de rendez-vous et établir mon devis. *
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Demander mon rendez-vous <?= icone('fleche') ?></button>
                    <p class="form-note">Gratuit · Sans engagement · Réponse sous 24h</p>

                    <p class="form-note rgpd-note">
                        Les informations recueillies sont destinées uniquement à Clean Elephant pour traiter votre demande
                        et ne sont jamais revendues. Elles sont conservées 3 ans après notre dernier contact.
                        Vous pouvez demander à y accéder, les rectifier ou les supprimer en écrivant à
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