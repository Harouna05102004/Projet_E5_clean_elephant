<?php
// Page contact - Clean Elephant
$succes = isset($_GET['succes']) ? true : false;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Clean Elephant</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="page-section">
    <div class="page-grid">
        <div class="page-content">
            <h1>Contactez-nous</h1>
            <p class="page-intro">Notre équipe est disponible pour répondre à toutes vos questions. Devis 100% gratuit et sans engagement.</p>

            <ul class="contact-info">
                <li>
                    <strong>EMAIL</strong>
                    <span>contact@cleanelephant.fr</span>
                </li>
                <li>
                    <strong>TÉLÉPHONE</strong>
                    <span>07 49 18 76 47</span>
                </li>
                <li>
                    <strong>ZONE D'INTERVENTION</strong>
                    <span>Montmagny &amp; Île-de-France</span>
                </li>
                <li>
                    <strong>HORAIRES</strong>
                    <span>Lundi - Samedi : 8h00 - 19h00</span>
                </li>
            </ul>

            <div class="social-links">
                <a href="#" class="btn btn-outline-dark">Instagram</a>
                <a href="#" class="btn btn-outline-dark">LinkedIn</a>
                <a href="#" class="btn btn-outline-dark">Facebook</a>
            </div>
        </div>

        <div class="page-form">
            <?php if ($succes): ?>
                <div class="alert-success">Votre message a bien été envoyé, nous vous répondrons rapidement.</div>
            <?php endif; ?>

            <form action="traitement_contact.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="prenom">PRÉNOM</label>
                        <input type="text" id="prenom" name="prenom" pattern="[^0-9]*" title="Le prénom ne doit pas contenir de chiffres" required>
                    </div>
                    <div class="form-group">
                        <label for="nom">NOM</label>
                        <input type="text" id="nom" name="nom" pattern="[^0-9]*" title="Le nom ne doit pas contenir de chiffres" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">EMAIL *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="telephone">TÉLÉPHONE</label>
                    <input type="tel" id="telephone" name="telephone">
                </div>

                <div class="form-group">
                    <label for="message">MESSAGE *</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">› ENVOYER</button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
