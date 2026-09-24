<footer class="site-footer">
    <div class="footer-grid">
        <div class="footer-col">
            <div class="footer-logo">
                <img src="images/logo-clean-elephant.png" alt="Clean Elephant">
                <div>
                    <strong>CLEAN ELEPHANT</strong>
                    <p>Nettoyage de vitres</p>
                </div>
            </div>
            <p class="footer-address">
                <strong>SIÈGE SOCIAL</strong><br>
                Montmagny, Île-de-France<br>
                contact@cleanelephant.fr<br>
                07 49 18 76 47
            </p>
        </div>

        <div class="footer-col">
            <strong class="footer-title">NAVIGATION</strong>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="particuliers.php">Particuliers</a></li>
                <li><a href="entreprises.php">Entreprises</a></li>
                <li><a href="devis.php">Devis offert</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <strong class="footer-title">NOUS VOUS RAPPELONS GRATUITEMENT</strong>
            <form action="traitement_rappel.php" method="POST" class="footer-form">
                <input type="text" name="nom" placeholder="Nom" pattern="[^0-9]*" title="Le nom ne doit pas contenir de chiffres" required>
                <input type="text" name="prenom" placeholder="Prénom" pattern="[^0-9]*" title="Le prénom ne doit pas contenir de chiffres" required>
                <input type="tel" name="telephone" placeholder="Téléphone" required>
                <button type="submit">ME RAPPELER</button>
            </form>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© 2026 Clean Elephant · Tous droits réservés · Mentions légales · Politique de confidentialité</p>
    </div>
</footer>
<script src="js/call-link.js"></script>
<script src="js/form-validation.js"></script>
