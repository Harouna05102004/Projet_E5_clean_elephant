<?php
// Page nettoyage pour professionnels - Clean Elephant
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage pour professionnels - Clean Elephant</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="page-section">
    <p class="breadcrumb"><a href="index.php">Accueil</a> › Entreprises</p>

    <h1 class="services-title">Nettoyage pour professionnels</h1>
    <p class="page-intro-wide">Bureaux, vitrines, immeubles, copropriétés — nous intervenons sur site avec du matériel professionnel, en horaires adaptés à votre activité. Devis gratuit, contrats d'entretien possibles.</p>

    <div class="services-grid">
        <a href="devis.php?type=bureaux" class="service-card">
            <div class="service-icon">🏢</div>
            <span>Bureaux</span>
        </a>
        <a href="devis.php?type=vitrine" class="service-card">
            <div class="service-icon">🏬</div>
            <span>Vitrine commerciale</span>
        </a>
        <a href="devis.php?type=immeuble" class="service-card">
            <div class="service-icon">🏙️</div>
            <span>Immeuble</span>
        </a>
        <a href="devis.php?type=copropriete" class="service-card">
            <div class="service-icon">🏘️</div>
            <span>Copropriété</span>
        </a>
        <a href="devis.php?type=hauteur" class="service-card">
            <div class="service-icon">↕️</div>
            <span>Grande hauteur</span>
        </a>
        <a href="devis.php?type=contrat" class="service-card">
            <div class="service-icon">📄</div>
            <span>Contrat d'entretien</span>
        </a>
    </div>

    <div class="services-actions">
        <a href="devis.php" class="btn btn-primary">› DEVIS GRATUIT</a>
        <a href="index.php" class="btn btn-outline-dark">RETOUR</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
