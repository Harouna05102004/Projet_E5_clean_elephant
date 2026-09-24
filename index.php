<?php
// Page d'accueil - Clean Elephant
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clean Elephant - Nettoyage de vitres à Montmagny et en Île-de-France</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-content">
            <span class="hero-badge">📍 MONTMAGNY · ÎLE-DE-FRANCE</span>
            <h1>Des vitres<br><span class="accent">propres.</span></h1>
            <p>Clean Elephant, spécialiste du nettoyage de vitres et surfaces vitrées pour particuliers et professionnels. Devis gratuit, résultat garanti.</p>
            <div class="hero-actions">
                <a href="devis.php" class="btn btn-primary">DEVIS GRATUIT 24H →</a>
                <a href="tel:+33749187647" class="btn btn-outline">📞 NOUS APPELER</a>
            </div>
            <div class="hero-trust">
                <span>✓ Devis 100% gratuit</span>
                <span>✓ Intervention rapide</span>
                <span>✓ Particuliers &amp; Pros</span>
            </div>
        </div>
        <div class="hero-image">
            <img src="images/hero-technicien.jpg" alt="Technicien Clean Elephant nettoyant une vitre">
            <div class="hero-image-badge">
                <span>🛡️</span>
                <div>
                    <strong>Résultat garanti</strong>
                    <span>Matériel professionnel</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== INTRO ===== -->
<section class="intro">
    <p>Clean Elephant est le spécialiste du nettoyage de vitres pour les particuliers et les professionnels.</p>
    <p class="devise">Notre devise : ponctualité, politesse et propreté.</p>
</section>

<!-- ===== POURQUOI CHOISIR CLEAN ELEPHANT ===== -->
<h2 class="features-title">POURQUOI CHOISIR CLEAN ELEPHANT</h2>
<div class="features-grid">
    <div class="feature-card">
        <div class="feature-icon">🕐</div>
        <h3>Devis gratuit en 24h</h3>
        <p>Recevez votre devis sous 24h, clair et sans engagement de votre part.</p>
        <a href="devis.php">› EN SAVOIR PLUS</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🧽</div>
        <h3>Matériel professionnel</h3>
        <p>Raclettes, perches télescopiques, produits adaptés — nous arrivons équipés pour un résultat net dès le premier passage.</p>
        <a href="devis.php">› EN SAVOIR PLUS</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon">⚡</div>
        <h3>Intervention rapide</h3>
        <p>Particuliers et professionnels — nous nous déplaçons à la demande sur Montmagny et toute l'Île-de-France.</p>
        <a href="devis.php">› EN SAVOIR PLUS</a>
    </div>
    <div class="feature-card">
        <div class="feature-icon">🛡️</div>
        <h3>Résultat garanti</h3>
        <p>Vous payez pour un résultat impeccable, pas pour un passage. Notre travail ne vous satisfait pas ? On revient.</p>
        <a href="devis.php">› EN SAVOIR PLUS</a>
    </div>
</div>

<!-- ===== AVIS CLIENTS ===== -->
<section class="reviews">
    <h2 class="features-title">AVIS CLIENTS</h2>
    <div class="reviews-grid">
        <div class="review-card">
            <div class="review-stars">★★★★★</div>
            <p>"Service impeccable, équipe très sérieuse. Je recommande sans hésiter !"</p>
            <div class="review-author">Sophie M.</div>
        </div>
        <div class="review-card">
            <div class="review-stars">★★★★★</div>
            <p>"Rapides, efficaces et soigneux. Mes baies vitrées n'ont jamais été aussi nettes."</p>
            <div class="review-author">Pierre L.</div>
        </div>
        <div class="review-card">
            <div class="review-stars">★★★★★</div>
            <p>"Très professionnel. Devis reçu rapidement, intervention le lendemain. Parfait."</p>
            <div class="review-author">Nathalie B.</div>
        </div>
    </div>
</section>

<!-- ===== GALERIE PHOTOS ===== -->
<div class="gallery">
    <img src="images/galerie-1.jpg" alt="Intervention Clean Elephant 1">
    <img src="images/galerie-2.jpg" alt="Intervention Clean Elephant 2">
    <img src="images/galerie-3.jpg" alt="Intervention Clean Elephant sur un immeuble">
</div>
<div class="gallery-cta">
    <a href="particuliers.php">› NOTRE ENGAGEMENT ÉCOLOGIQUE</a>
</div>

<!-- ===== AVANT / APRÈS ===== -->
<h2 class="before-after-title">AVANT / APRÈS</h2>
<div class="before-after">
    <div class="ba-item">
        <img src="images/avant.jpg" alt="Vitre encrassée avant nettoyage">
        <div class="ba-label avant">AVANT</div>
    </div>
    <div class="ba-item">
        <img src="images/apres.jpg" alt="Vitre nette après nettoyage">
        <div class="ba-label apres">APRÈS</div>
    </div>
</div>

<!-- ===== CTA FINAL ===== -->
<div class="cta-final">
    <h2>Prêt pour des vitres impeccables ?</h2>
    <p>Réponse sous 24h · Montmagny &amp; Île-de-France</p>
    <div class="hero-actions">
        <a href="devis.php" class="btn btn-primary">OBTENIR MON DEVIS →</a>
        <a href="tel:+33749187647" class="btn btn-outline">📞 NOUS APPELER</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
