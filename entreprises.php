<?php
/*
 * entreprises.php — Prestations pour les professionnels
 * Même structure que particuliers.php : seuls les textes et les prestations changent.
 */
require_once __DIR__ . '/includes/securite.php';   // en premier : session pour le formulaire de rappel du pied de page
require_once __DIR__ . '/includes/icones.php';

$page_active = 'pros';

$prestations = [
    ['cle' => 'contrat',     'titre' => "Contrat d'entretien", 'texte' => "Un passage régulier, planifié selon votre activité : vos vitres restent propres sans que vous ayez à y penser."],
    ['cle' => 'bureaux',     'titre' => 'Bureaux',             'texte' => "Fenêtres et cloisons vitrées de vos locaux, en horaires adaptés à votre activité."],
    ['cle' => 'vitrine',     'titre' => 'Vitrine commerciale', 'texte' => "Une vitrine nette, c'est la première impression que vous donnez à vos clients."],
    ['cle' => 'immeuble',    'titre' => 'Immeuble',            'texte' => "Halls, cages d'escalier et parties vitrées des immeubles."],
    ['cle' => 'copropriete', 'titre' => 'Copropriété',         'texte' => "Pour les syndics et conseils syndicaux : entretien des surfaces vitrées communes."],
    ['cle' => 'hauteur',     'titre' => 'Grande hauteur',      'texte' => "Vitres difficiles d'accès, traitées avec du matériel adapté."],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage de vitres pour professionnels | Clean Elephant</title>
    <meta name="description" content="Nettoyage de vitres de bureaux, vitrines, immeubles et copropriétés à Montmagny et en Île-de-France. Contrats d'entretien, devis gratuit.">
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
                    <li aria-current="page">Professionnels</li>
                </ol>
            </nav>
            <!-- Interrupteur : on change de page en un clic si on s'est trompé de profil -->
            <nav class="switch" aria-label="Choisir votre profil">
                <a href="particuliers.php">Je suis un particulier</a>
                <a href="entreprises.php" aria-current="page">Je suis un professionnel</a>
            </nav>
            <div class="page-hero__grid">
                <div>
                    <p class="kicker">Nos services · Professionnels</p>
                    <h1 class="page-hero__title" id="titre-page">
                        <span class="line"><span>Votre image,</span></span>
                        <span class="line"><span><em>sans une trace.</em></span></span>
                    </h1>
                </div>
                <p class="lead">Bureaux, vitrines, immeubles, copropriétés : nous intervenons sur site avec du matériel professionnel, en horaires adaptés à votre activité. Contrats d'entretien possibles.</p>
            </div>
        </div>
    </section>

    <section class="section" aria-label="Nos prestations pour les professionnels">
        <div class="container">
            <?php include __DIR__ . '/includes/tuiles.php'; ?>
        </div>
    </section>

    <?php include __DIR__ . '/includes/processus.php'; ?>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>