<?php
// Page devis - Clean Elephant
$succes = isset($_GET['succes']) ? true : false;

// Pré-cocher un type si on arrive depuis particuliers.php (ex: devis.php?type=maison)
$type_preselectionne = isset($_GET['type']) ? $_GET['type'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis gratuit en 24h - Clean Elephant</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="page-section">
    <div class="page-grid form-wide">
        <div class="page-content">
            <h1>Devis gratuit<br>en 24h</h1>
            <p class="page-intro">Remplissez le formulaire, nous vous recontactons sous 24h avec un devis clair et sans surprise.</p>
            <img src="images/hero-technicien.jpg" alt="Technicien Clean Elephant" class="devis-image">
        </div>

        <div class="page-form">
            <?php if ($succes): ?>
                <div class="alert-success">Votre demande de devis a bien été envoyée. Nous vous recontactons sous 24h.</div>
            <?php endif; ?>

            <form action="traitement_devis.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">NOM *</label>
                        <input type="text" id="nom" name="nom" placeholder="Votre nom" pattern="[^0-9]*" title="Le nom ne doit pas contenir de chiffres" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">PRÉNOM *</label>
                        <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" pattern="[^0-9]*" title="Le prénom ne doit pas contenir de chiffres" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telephone">TÉLÉPHONE *</label>
                    <input type="tel" id="telephone" name="telephone" placeholder="Votre numéro de téléphone" required>
                </div>

                <div class="form-group">
                    <label for="email">EMAIL *</label>
                    <input type="email" id="email" name="email" placeholder="Votre adresse email" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="adresse">ADRESSE</label>
                        <input type="text" id="adresse" name="adresse" placeholder="Adresse postale">
                    </div>
                    <div class="form-group">
                        <label for="ville">VILLE</label>
                        <input type="text" id="ville" name="ville" placeholder="Ville">
                    </div>
                </div>

                <div class="form-group">
                    <label>TYPE DE NETTOYAGE</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Appartement" <?php if ($type_preselectionne === 'appartement') echo 'checked'; ?>>
                            Appartement
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Maison" <?php if ($type_preselectionne === 'maison') echo 'checked'; ?>>
                            Maison
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Véranda" <?php if ($type_preselectionne === 'veranda') echo 'checked'; ?>>
                            Véranda
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Vitrine commerciale" <?php if ($type_preselectionne === 'vitrine') echo 'checked'; ?>>
                            Vitrine commerciale
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Grande hauteur" <?php if ($type_preselectionne === 'hauteur') echo 'checked'; ?>>
                            Grande hauteur
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Façades" <?php if ($type_preselectionne === 'facade') echo 'checked'; ?>>
                            Façades
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Bureaux" <?php if ($type_preselectionne === 'bureaux') echo 'checked'; ?>>
                            Bureaux
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Immeuble" <?php if ($type_preselectionne === 'immeuble') echo 'checked'; ?>>
                            Immeuble
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Copropriété" <?php if ($type_preselectionne === 'copropriete') echo 'checked'; ?>>
                            Copropriété
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="type_nettoyage[]" value="Contrat d'entretien" <?php if ($type_preselectionne === 'contrat') echo 'checked'; ?>>
                            Contrat d'entretien
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="message">MESSAGE (optionnel)</label>
                    <textarea id="message" name="message" rows="4" placeholder="Précisions sur votre demande..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">› ENVOYER MA DEMANDE DE DEVIS</button>

                <p class="form-note">Gratuit · Sans engagement · Réponse sous 24h</p>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
