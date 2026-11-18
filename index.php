<?php
/*
 * index.php : Page d'accueil de Clean Elephant
 *
 * securite.php est inclus EN PREMIER : il démarre la session, nécessaire au
 * jeton CSRF du formulaire de rappel présent dans le pied de page.
 */
require_once __DIR__ . '/includes/securite.php';
require_once __DIR__ . '/includes/icones.php';

$page_active = '';   // aucun lien du menu à souligner sur l'accueil

// Icônes SVG réutilisées plusieurs fois dans la page (aria-hidden : purement décoratives)
$icone_fleche = '<svg class="icon-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$icone_tel    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>';
$icone_check  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
$icone_etoile = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></svg>';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clean Elephant | Nettoyage de vitres à Montmagny et en Île-de-France</title>
    <!-- Description affichée par Google sous le titre dans les résultats de recherche -->
    <meta name="description" content="Nettoyage de vitres pour particuliers et professionnels à Montmagny et en Île-de-France. Devis gratuit sous 24h, matériel professionnel, résultat garanti.">
    <meta name="theme-color" content="#0B1E3A">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- id="main-content" : cible du lien d'évitement « Aller au contenu » -->
<main id="main-content">

    <!-- =====================================================
         1. HERO : texte à gauche, photo à droite (asymétrique)
         ===================================================== -->
    <section class="hero" aria-labelledby="titre-hero">
        <div class="container hero__grid">
            <div class="hero__content">
                <p class="badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Montmagny · Île-de-France
                </p>

                <!-- Un seul h1 par page : c'est le titre principal pour Google et les lecteurs d'écran -->
                <!-- Chaque ligne est un « masque » (overflow: hidden) : le texte glisse de bas en haut à l'intérieur -->
                <h1 class="hero__title" id="titre-hero">
                    <span class="line"><span>Des vitres propres.</span></span>
                    <span class="line"><span><span class="hero__title-accent">Sans compromis.</span></span></span>
                </h1>

                <p class="hero__lead">
                    Particuliers et professionnels : nous nettoyons vos vitres avec du matériel professionnel et trois règles simples :
                    <strong>ponctualité, politesse et propreté</strong>.
                </p>

                <div class="hero__actions">
                    <a href="devis.php" class="btn btn-primary">Devis gratuit 24h <?= $icone_fleche ?></a>
                    <a href="tel:+33749187647" class="btn btn--ghost"><?= $icone_tel ?> Nous appeler</a>
                </div>

                <ul class="pills" aria-label="Nos garanties">
                    <li><?= $icone_check ?> Devis 100 % gratuit</li>
                    <li><?= $icone_check ?> Intervention rapide</li>
                    <li><?= $icone_check ?> Particuliers &amp; pros</li>
                </ul>
            </div>

            <figure class="hero__media">
                <div class="hero__frame">
                    <!-- fetchpriority="high" : image la plus importante, chargée en premier -->
                    <img src="images/hero-technicien.jpg" alt="Technicien Clean Elephant en tenue nettoyant une grande baie vitrée à la raclette"
                         width="900" height="1080" fetchpriority="high">
                    <span class="hero__shine" aria-hidden="true"></span>
                </div>
                <figcaption class="hero__badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    <div>
                        <strong>Résultat garanti</strong>
                        <span>Matériel professionnel</span>
                    </div>
                </figcaption>
            </figure>
        </div>
    </section>


    <!-- =====================================================
         BANDEAU DÉFILANT : les prestations, en boucle infinie.
         La liste est écrite deux fois : quand la première a fini de
         défiler, la seconde est exactement à sa place -> boucle sans saut.
         La copie est cachée aux lecteurs d'écran (aria-hidden).
         ===================================================== -->
    <div class="marquee-wrap">
    <div class="marquee" aria-label="Nos prestations">
        <div class="marquee__track">
            <?php
            $prestations = ['Appartements', 'Maisons', 'Vérandas', 'Vitrines commerciales', 'Bureaux', 'Immeubles', 'Copropriétés', 'Grande hauteur', 'Façades', "Contrats d'entretien"];
            $etincelle = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2c.6 4.5 2.5 6.9 7 7.5v1c-4.5.6-6.4 3-7 7.5h-1c-.6-4.5-2.5-6.9-7-7.5v-1c4.5-.6 6.4-3 7-7.5z"/></svg>';
            for ($copie = 0; $copie < 2; $copie++): ?>
                <ul class="marquee__list"<?= $copie === 1 ? ' aria-hidden="true"' : '' ?>>
                    <?php foreach ($prestations as $prestation): ?>
                        <li><?= e($prestation) ?> <?= $etincelle ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endfor; ?>
        </div>
    </div>
    </div>


    <!-- =====================================================
         AIGUILLAGE : le visiteur choisit tout de suite son profil
         (deux grandes cartes au lieu d'un menu déroulant caché).
         ===================================================== -->
    <section class="chooser" aria-labelledby="titre-choix">
        <div class="container">
            <div class="chooser__head">
                <h2 class="chooser__title" id="titre-choix">Vous êtes :</h2>
                <p class="chooser__hint">Pas sûr ? <a href="devis.php">Demandez directement votre devis</a>, on s'occupe du reste.</p>
            </div>
            <div class="chooser__grid">
                <a class="choice" href="particuliers.php" data-reveal>
                    <span class="choice__icon"><?= icone('maison') ?></span>
                    <span class="choice__label">Un particulier</span>
                    <span class="choice__text">Maison, appartement, véranda</span>
                    <span class="choice__go"><?= icone('fleche') ?></span>
                </a>
                <a class="choice" href="entreprises.php" data-reveal>
                    <span class="choice__icon"><?= icone('bureaux') ?></span>
                    <span class="choice__label">Un professionnel</span>
                    <span class="choice__text">Bureaux, vitrines, copropriétés, contrats d'entretien</span>
                    <span class="choice__go"><?= icone('fleche') ?></span>
                </a>
            </div>
        </div>
    </section>


    <!-- =====================================================
         2. MANIFESTE : la devise de l'entreprise + chiffres clés
         ===================================================== -->
    <section class="manifesto section" aria-labelledby="titre-manifeste">
        <div class="container">
            <div class="manifesto__grid">
                <div>
                    <p class="kicker" id="titre-manifeste">Notre devise</p>
                    <!-- data-reveal="wipe" : le mot apparaît de gauche à droite, comme essuyé -->
                    <ul class="manifesto__words">
                        <li data-reveal="wipe">Ponctualité.</li>
                        <li data-reveal="wipe">Politesse.</li>
                        <li data-reveal="wipe">Propreté.</li>
                    </ul>
                </div>
                <div class="manifesto__text" data-reveal>
                    <p>Clean Elephant est né en 2024 à Montmagny d'une idée simple : <strong>un nettoyage de vitres, ça se voit</strong>. Pas de traces, pas de retard, pas de mauvaise surprise sur le devis.</p>
                    <p>Nous arrivons à l'heure, équipés, nous respectons votre intérieur ou votre commerce, et nous ne partons que lorsque le résultat est impeccable.</p>
                </div>
            </div>

            <!-- Chiffres clés : data-target = valeur finale, animée par js/main.js.
                 Uniquement des faits vérifiables (pas de chiffres inventés). -->
            <ul class="stats">
                <li data-reveal>
                    <span class="stats__value"><span data-count-target="24">24</span><span class="stats__suffix">h</span></span>
                    <span class="stats__label">pour recevoir votre devis</span>
                </li>
                <li data-reveal>
                    <span class="stats__value"><span data-count-target="6">6</span><span class="stats__suffix">j/7</span></span>
                    <span class="stats__label">du lundi au samedi, de 8h à 19h</span>
                </li>
                <li data-reveal>
                    <span class="stats__value"><span data-count-target="100">100</span><span class="stats__suffix">%</span></span>
                    <span class="stats__label">des devis gratuits et sans engagement</span>
                </li>
                <li data-reveal>
                    <span class="stats__value">2024</span>
                    <span class="stats__label">création à Montmagny (95)</span>
                </li>
            </ul>
        </div>
    </section>


    <!-- =====================================================
         3. POURQUOI NOUS CHOISIR : intro collante à gauche, liste à droite
         ===================================================== -->
    <section class="section" aria-labelledby="titre-avantages">
        <div class="container benefits__grid">
            <div class="benefits__intro">
                <p class="kicker">Pourquoi Clean Elephant</p>
                <h2 class="section-title" id="titre-avantages">Un travail net, du premier appel au dernier coup de raclette.</h2>
                <p class="lead">Vous payez pour un résultat, pas pour un passage.</p>
                <a href="devis.php" class="btn btn-primary">Demander mon devis <?= $icone_fleche ?></a>
            </div>

            <!-- Liste ordonnée : les numéros 01 à 04 sont générés en CSS (counter) -->
            <ol class="benefit-list">
                <li class="benefit" data-reveal>
                    <span class="benefit__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    </span>
                    <div>
                        <h3>Devis gratuit en 24h</h3>
                        <p>Un devis clair, détaillé et sans engagement, reçu sous 24h.</p>
                    </div>
                </li>
                <li class="benefit" data-reveal>
                    <span class="benefit__icon">
                        <!-- Icône raclette -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 9h16"/><path d="M6 9V6h12v3"/><path d="M12 9v12"/></svg>
                    </span>
                    <div>
                        <h3>Matériel professionnel</h3>
                        <p>Raclettes, perches télescopiques, produits adaptés : nous arrivons équipés pour un résultat net dès le premier passage.</p>
                    </div>
                </li>
                <li class="benefit" data-reveal>
                    <span class="benefit__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 2 4 14h7l-1 8 9-12h-7z"/></svg>
                    </span>
                    <div>
                        <h3>Intervention rapide</h3>
                        <p>Particuliers et professionnels : nous nous déplaçons à Montmagny et dans toute l'Île-de-France.</p>
                    </div>
                </li>
                <li class="benefit" data-reveal>
                    <span class="benefit__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    </span>
                    <div>
                        <h3>Résultat garanti</h3>
                        <p>Le résultat ne vous satisfait pas ? Nous revenons.</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>


    <!-- =====================================================
         4. AVANT / APRÈS : comparateur façon raclette
         ===================================================== -->
    <section class="compare section" aria-labelledby="titre-comparaison">
        <div class="container compare__grid">
            <div data-reveal>
                <p class="kicker">Avant / Après</p>
                <h2 class="section-title" id="titre-comparaison">Passez la raclette vous-même.</h2>
                <p class="lead">Faites glisser la poignée dorée pour comparer la vitre avant et après notre passage.</p>
                <p class="compare__hint">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 7-5 5 5 5"/><path d="m16 7 5 5-5 5"/></svg>
                    À la souris, au doigt ou avec les flèches du clavier
                </p>
            </div>

            <!-- Structure : image « avant » en fond, image « après » par-dessus (rognée selon --pos),
                 et un input range invisible qui pilote --pos. -->
            <div class="comparison-slider" data-reveal data-demo>
                <img src="images/avant.jpg" alt="Vitre encrassée avant le nettoyage" width="1200" height="900" loading="lazy">
                <div class="comparison-slider__after">
                    <img src="images/apres.jpg" alt="La même vitre, nette, après le nettoyage" width="1200" height="900" loading="lazy">
                </div>
                <div class="comparison-slider__handle" aria-hidden="true">
                    <span class="comparison-slider__grip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6-6 6 6 6"/><path d="m15 6 6 6-6 6"/></svg>
                    </span>
                </div>
                <span class="comparison-slider__label comparison-slider__label--apres" aria-hidden="true">Après</span>
                <span class="comparison-slider__label comparison-slider__label--avant" aria-hidden="true">Avant</span>
                <input class="comparison-slider__range" type="range" min="0" max="100" value="50"
                       aria-label="Comparer avant et après : glisser vers la droite pour voir la vitre nettoyée">
            </div>
        </div>
    </section>


    <!-- =====================================================
         4 bis. EN VIDÉO : une vraie intervention, filmée par l'équipe
         Vidéo muette (obligatoire pour la lecture automatique), lue
         seulement quand elle est à l'écran, avec un bouton pause.
         ===================================================== -->
    <section class="video-demo section" aria-labelledby="titre-video">
        <div class="container video-demo__grid">
            <div class="video-demo__text" data-reveal>
                <p class="kicker">En vidéo</p>
                <h2 class="section-title" id="titre-video">Un coup de raclette, et le reflet revient.</h2>
                <p class="lead">Filmé par notre équipe pendant une intervention : savon, raclette professionnelle, essuyage des bords. Pas de traces, pas de coulures.</p>
                <ul class="pills">
                    <li><?= icone('check') ?> Matériel professionnel</li>
                    <li><?= icone('check') ?> Sans traces</li>
                    <li><?= icone('check') ?> Résultat garanti</li>
                </ul>
                <a class="btn btn-primary" href="devis.php">Demander mon devis <?= $icone_fleche ?></a>
            </div>

            <figure class="video-demo__media" data-reveal>
                <video class="video-demo__video" muted loop playsinline preload="none"
                       poster="videos/demonstration-poster.jpg" width="540" height="960"
                       aria-label="Vidéo : un technicien Clean Elephant nettoie un miroir rond à la raclette">
                    <source src="videos/demonstration.mp4" type="video/mp4">
                </video>
                <button type="button" class="video-demo__toggle" aria-label="Mettre la vidéo en pause" data-video-toggle>
                    <span class="video-demo__icon video-demo__icon--pause" aria-hidden="true"></span>
                </button>
                <figcaption>Intervention réelle · 11 secondes</figcaption>
            </figure>
        </div>
    </section>


    <!-- =====================================================
         5. GALERIE : une grande photo + deux petites
         ===================================================== -->
    <section class="section" aria-labelledby="titre-galerie">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="kicker">Nos réalisations</p>
                    <h2 class="section-title" id="titre-galerie">Sur le terrain, chaque semaine.</h2>
                </div>
                <a class="link-arrow" href="particuliers.php">Voir nos prestations <?= $icone_fleche ?></a>
            </div>

            <!-- Adapte les légendes et les alt au contenu réel de chaque photo -->
            <div class="gallery-grid">
                <figure class="gallery-item" data-reveal="wipe">
                    <img src="images/galerie-3.jpg" alt="Intervention Clean Elephant sur un immeuble" width="1200" height="1000" loading="lazy">
                    <figcaption>Immeuble</figcaption>
                </figure>
                <figure class="gallery-item" data-reveal="wipe">
                    <img src="images/galerie-1.jpg" alt="Intervention Clean Elephant chez un client" width="900" height="600" loading="lazy">
                    <figcaption>Intervention</figcaption>
                </figure>
                <figure class="gallery-item" data-reveal="wipe">
                    <img src="images/galerie-2.jpg" alt="Intervention Clean Elephant chez un client" width="900" height="600" loading="lazy">
                    <figcaption>Intervention</figcaption>
                </figure>
            </div>
        </div>
    </section>


    <!-- =====================================================
         6. AVIS CLIENTS
         À remplacer par de VRAIS avis (fiche Google de l'entreprise, ou avis
         demandés aux clients) : publier de faux avis est interdit.
         ===================================================== -->
    <section class="reviews section" aria-labelledby="titre-avis">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="kicker">Avis clients</p>
                    <h2 class="section-title" id="titre-avis">Ils en parlent mieux que nous.</h2>
                </div>
            </div>

            <div class="reviews-grid">
                <figure class="review-card" data-reveal>
                    <div class="review-stars" role="img" aria-label="5 étoiles sur 5"><?= str_repeat($icone_etoile, 5) ?></div>
                    <blockquote><p>Service impeccable, équipe très sérieuse. Je recommande sans hésiter !</p></blockquote>
                    <figcaption>Sophie M. <span>Avis client</span></figcaption>
                </figure>
                <figure class="review-card" data-reveal>
                    <div class="review-stars" role="img" aria-label="5 étoiles sur 5"><?= str_repeat($icone_etoile, 5) ?></div>
                    <blockquote><p>Rapides, efficaces et soigneux. Mes baies vitrées n'ont jamais été aussi nettes.</p></blockquote>
                    <figcaption>Pierre L. <span>Avis client</span></figcaption>
                </figure>
                <figure class="review-card" data-reveal>
                    <div class="review-stars" role="img" aria-label="5 étoiles sur 5"><?= str_repeat($icone_etoile, 5) ?></div>
                    <blockquote><p>Très professionnel. Devis reçu rapidement, intervention le lendemain. Parfait.</p></blockquote>
                    <figcaption>Nathalie B. <span>Avis client</span></figcaption>
                </figure>
            </div>
        </div>
    </section>


    <!-- =====================================================
         7. APPEL À L'ACTION FINAL
         ===================================================== -->
    <section class="cta-final section" aria-labelledby="titre-cta">
        <div class="container cta-final__inner">
            <div data-reveal>
                <h2 id="titre-cta">Prêt pour des vitres impeccables ?</h2>
                <p>Votre demande en moins de 2 minutes. Réponse sous 24h · Montmagny &amp; Île-de-France.</p>
            </div>
            <div class="hero__actions" data-reveal>
                <a href="devis.php" class="btn btn-primary">Obtenir mon devis <?= $icone_fleche ?></a>
                <a href="tel:+33749187647" class="btn btn--light"><?= $icone_tel ?> 07 49 18 76 47</a>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>