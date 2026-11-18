<?php
/*
 * particuliers.php — Prestations pour les particuliers
 * Chaque tuile mène à devis.php?type=... pour pré-cocher la bonne case.
 */
require_once __DIR__ . '/includes/securite.php';   // en premier : session pour le formulaire de rappel du pied de page
require_once __DIR__ . '/includes/icones.php';

$page_active = 'particuliers';

// Le contenu des tuiles est séparé du HTML : on modifie un texte ici, pas dans la mise en page
$prestations = [
    ['cle' => 'maison',      'titre' => 'Maison',              'texte' => "Fenêtres, baies vitrées et portes-fenêtres de toute la maison."],
    ['cle' => 'appartement', 'titre' => 'Appartement',         'texte' => "Fenêtres et baies vitrées de votre appartement, sans rien salir chez vous."],
    ['cle' => 'veranda',     'titre' => 'Véranda',             'texte' => "Parois et toiture vitrées pour retrouver toute la lumière."],
    ['cle' => 'vitrine',     'titre' => 'Vitrine commerciale', 'texte' => "Votre commerce est en bas de chez vous ? Nous nous occupons aussi de la vitrine."],
    ['cle' => 'hauteur',     'titre' => 'Grande hauteur',      'texte' => "Vitres difficiles d'accès, traitées avec du matériel adapté."],
    ['cle' => 'facade',      'titre' => 'Façades',             'texte' => "Surfaces vitrées en façade, pour un résultat net de l'extérieur."],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage de vitres pour particuliers | Clean Elephant</title>
    <meta name="description" content="Nettoyage de vitres de maisons, appartements et vérandas à Montmagny et en Île-de-France. Devis gratuit sous 24h.">
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
                    <li aria-current="page">Particuliers</li>
                </ol>
            </nav>
            <!-- Interrupteur : on change de page en un clic si on s'est trompé de profil -->
            <nav class="switch" aria-label="Choisir votre profil">
                <a href="particuliers.php" aria-current="page">Je suis un particulier</a>
                <a href="entreprises.php">Je suis un professionnel</a>
            </nav>
            <div class="page-hero__grid">
                <div>
                    <p class="kicker">Nos services · Particuliers</p>
                    <h1 class="page-hero__title" id="titre-page">
                        <span class="line"><span>Des vitres nettes,</span></span>
                        <span class="line"><span><em>chez vous.</em></span></span>
                    </h1>
                </div>
                <p class="lead">Appartements, maisons, vérandas : nous intervenons chez vous, à l'heure, avec du matériel professionnel. Devis gratuit et sans engagement.</p>
            </div>
        </div>
    </section>

    <section class="section" aria-label="Nos prestations pour les particuliers">
        <div class="container">
            <?php include __DIR__ . '/includes/tuiles.php'; ?>
        </div>
    </section>

    <?php include __DIR__ . '/includes/processus.php'; ?>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>