<?php
// Page nettoyage pour particuliers - Clean Elephant
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nettoyage pour particuliers - Clean Elephant</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="page-section">
    <p class="breadcrumb"><a href="index.php">Accueil</a> › Particuliers</p>

    <h1 class="services-title">Nettoyage pour particuliers</h1>
    <p class="page-intro-wide">Appartements, maisons, vérandas — nous intervenons chez vous pour des vitres nettes. Devis gratuit, intervention rapide.</p>

    <div class="services-grid">
        <a href="devis.php?type=appartement" class="service-card">
            <div class="service-icon">🏢</div>
            <span>Appartement</span>
        </a>
        <a href="devis.php?type=maison" class="service-card">
            <div class="service-icon">🏠</div>
            <span>Maison</span>
        </a>
        <a href="devis.php?type=veranda" class="service-card">
            <div class="service-icon">🌿</div>
            <span>Véranda</span>
        </a>
        <a href="devis.php?type=vitrine" class="service-card">
            <div class="service-icon">🏬</div>
            <span>Vitrine commerciale</span>
        </a>
        <a href="devis.php?type=hauteur" class="service-card">
            <div class="service-icon">↕️</div>
            <span>Grande hauteur</span>
        </a>
        <a href="devis.php?type=facade" class="service-card">
            <div class="service-icon">▦</div>
            <span>Façades</span>
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
