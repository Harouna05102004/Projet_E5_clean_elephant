<?php
/*
 * includes/processus.php — Bloc « Comment ça se passe » + appel à l'action
 * Partagé par particuliers.php et entreprises.php (écrit une seule fois).
 */
$base = $base ?? '';
?>
<section class="process section" aria-labelledby="titre-processus">
    <div class="container">
        <div class="section-head">
            <div>
                <p class="kicker">Comment ça se passe</p>
                <h2 class="section-title" id="titre-processus">Trois étapes, zéro surprise.</h2>
            </div>
        </div>

        <ol class="process__list">
            <li class="process__step" data-reveal>
                <span class="process__num" aria-hidden="true">01</span>
                <h3>Vous faites votre demande</h3>
                <p>En ligne en 2 minutes, ou par téléphone au 07 49 18 76 47. Vous choisissez une date et un créneau.</p>
            </li>
            <li class="process__step" data-reveal>
                <span class="process__num" aria-hidden="true">02</span>
                <h3>Un technicien évalue et chiffre</h3>
                <p>Il se déplace, évalue votre besoin et vous remet un devis clair, gratuit et sans engagement.</p>
            </li>
            <li class="process__step" data-reveal>
                <span class="process__num" aria-hidden="true">03</span>
                <h3>Nous intervenons</h3>
                <p>Le jour convenu, à l'heure et avec le matériel adapté. Vous suivez tout depuis votre espace client.</p>
            </li>
        </ol>
    </div>
</section>

<section class="cta-final section" aria-labelledby="titre-cta">
    <div class="container cta-final__inner">
        <div data-reveal>
                        <h2 id="titre-cta">Des traces sur vos vitres ? On s'en occupe.</h2>
            <p>Devis gratuit sous 24h, sans engagement · Montmagny &amp; Île-de-France.</p>
        </div>
        <div class="hero__actions" data-reveal>
            <a href="<?= $base ?>devis.php" class="btn btn-primary">Demander mon devis <?= icone('fleche') ?></a>
            <a href="tel:+33749187647" class="btn btn--light"><?= icone('telephone') ?> 07 49 18 76 47</a>
        </div>
    </div>
</section>