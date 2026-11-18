<?php
/*
 * includes/footer.php — Pied de page commun
 *
 * Contient : le formulaire « Nous vous rappelons gratuitement » (protégé par
 * le jeton CSRF et le champ piège), les infos de l'entreprise, la zone
 * d'intervention (#zone, ciblée par le menu), les liens légaux, et le script.
 *
 * IMPORTANT : la page qui inclut ce fichier doit commencer par
 *   require_once __DIR__ . '/includes/securite.php';
 * AVANT tout HTML, pour que la session (et donc le jeton CSRF) fonctionne.
 * Le require_once ci-dessous n'est qu'un filet de sécurité.
 */
require_once __DIR__ . '/securite.php';
$base = $base ?? '';

// Message flash laissé par traitement_rappel.php (affiché une seule fois)
$rappelMessage = $_SESSION['rappel_message'] ?? null;
unset($_SESSION['rappel_message']);
?>
<footer class="site-footer">
    <div class="container footer__grid">

        <!-- Carte de rappel -->
        <section class="callback-card" id="rappel" aria-labelledby="titre-rappel">
            <p class="kicker">Rappel gratuit</p>
            <h2 id="titre-rappel">Nous vous rappelons gratuitement</h2>
            <p>Laissez-nous votre numéro : nous vous rappelons dans la journée, du lundi au samedi de 8h à 19h.</p>

            <?php if ($rappelMessage): ?>
                <!-- role="status" : le message est annoncé par les lecteurs d'écran -->
                <div class="<?= $rappelMessage['type'] === 'succes' ? 'alert-success' : 'alert-error' ?>" role="status">
                    <?= e($rappelMessage['texte']) ?>
                </div>
            <?php endif; ?>

            <form class="callback-form" action="<?= $base ?>traitement_rappel.php" method="POST">
                <?= csrf_champ() ?>
                <?= honeypot_champ() ?>

                <div class="form-group">
                    <label for="rappel-nom">Nom</label>
                    <input type="text" id="rappel-nom" name="nom" maxlength="100" autocomplete="family-name" required>
                </div>
                <div class="form-group">
                    <label for="rappel-prenom">Prénom</label>
                    <input type="text" id="rappel-prenom" name="prenom" maxlength="100" autocomplete="given-name" required>
                </div>
                <div class="form-group form-group--full">
                    <label for="rappel-telephone">Téléphone</label>
                    <input type="tel" id="rappel-telephone" name="telephone" maxlength="20" autocomplete="tel" required>
                </div>
                <button type="submit" class="btn btn-primary">Être rappelé gratuitement</button>
                <p class="form-note rgpd-note form-group--full">
                    Votre numéro sert uniquement à vous rappeler au sujet de votre demande.
                    <a href="<?= $base ?>confidentialite.php">Politique de confidentialité</a>.
                </p>
            </form>
        </section>

        <!-- Infos entreprise -->
        <div>
            <div class="footer__brand">
                                <span class="brand__mark brand__mark--lg"><img src="<?= $base ?>images/logo-clean-elephant.png" alt="" width="52" height="52"></span>
                <div>
                    <strong>Clean Elephant</strong>
                    Nettoyage de vitres depuis 2024
                </div>
            </div>
            <p>Ponctualité, politesse et propreté : le nettoyage de vitres pour les particuliers et les professionnels, à Montmagny et dans toute l'Île-de-France.</p>

            <div class="footer__cols">
                <div>
                    <h2 class="footer__title">Contact</h2>
                    <ul class="footer__list">
                        <li><a href="tel:+33749187647">07 49 18 76 47</a></li>
                        <li><a href="mailto:contact@cleanelephant.fr">contact@cleanelephant.fr</a></li>
                        <li>Du lundi au samedi<br>de 8h à 19h</li>
                    </ul>
                </div>
                <div>
                    <h2 class="footer__title">Services</h2>
                    <ul class="footer__list">
                        <li><a href="<?= $base ?>particuliers.php">Particuliers</a></li>
                        <li><a href="<?= $base ?>entreprises.php">Professionnels</a></li>
                        <li><a href="<?= $base ?>devis.php">Devis gratuit</a></li>
                        <li><a href="<?= $base ?>espace-client/index.php">Suivi de ma demande</a></li>
                    </ul>
                </div>
            </div>

            <!-- id="zone" : cible du lien « Zone d'intervention » du menu -->
            <!-- Adapte la liste aux villes / départements réellement couverts -->
            <div class="footer__zone" id="zone">
                <h2 class="footer__title">Zone d'intervention</h2>
                <ul class="zone-badges">
                    <li>Montmagny</li>
                    <li>Val-d'Oise (95)</li>
                    <li>Toute l'Île-de-France</li>
                </ul>

                <!-- Carte OpenStreetMap chargée UNIQUEMENT après un clic (RGPD) :
                     avant le clic, aucune requête ne part vers un site extérieur.
                     main.js lit data-map-src et crée l'<iframe> au moment du clic. -->
                <div class="map-consent"
                     data-map-src="https://www.openstreetmap.org/export/embed.html?bbox=1.95%2C48.70%2C2.75%2C49.12&amp;layer=mapnik&amp;marker=48.9727%2C2.3459">
                    <p>Carte fournie par OpenStreetMap. En l'affichant, votre navigateur se connecte à leurs serveurs.</p>
                    <button type="button" class="btn btn--light map-consent__btn">Afficher la carte</button>
                    <a class="map-consent__link" href="https://www.openstreetmap.org/?mlat=48.9727&amp;mlon=2.3459#map=10/48.97/2.35" target="_blank" rel="noopener">Ouvrir dans OpenStreetMap</a>
                </div>
            </div>

            <!-- Réseaux sociaux : remplace les # par les vraies adresses des pages -->
            <div class="socials">
                <a href="#" aria-label="Clean Elephant sur Instagram">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                </a>
                <a href="#" aria-label="Clean Elephant sur LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9.5h4V21H3zM9.5 9.5h3.8v1.6h.1c.5-1 1.8-2 3.8-2 4 0 4.8 2.6 4.8 6V21h-4v-5.2c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V21h-4z"/></svg>
                </a>
                <a href="#" aria-label="Clean Elephant sur Facebook">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 8.5V6.8c0-.8.2-1.3 1.4-1.3H17V2.2C16.7 2.1 15.6 2 14.4 2 11.8 2 10 3.6 10 6.5v2H7.3v3.7H10V22h4v-9.8h2.8l.4-3.7z"/></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="container footer__bottom">
        <p>© <?= date('Y') ?> Clean Elephant · Montmagny (95)</p>
        <ul>
            <li><a href="<?= $base ?>mentions-legales.php">Mentions légales</a></li>
            <li><a href="<?= $base ?>confidentialite.php">Confidentialité</a></li>
        </ul>
    </div>
</footer>

<!-- defer : le script se charge sans bloquer l'affichage, puis s'exécute une fois la page prête -->
<script src="<?= $base ?>js/main.js?v=<?= filemtime(__DIR__ . '/../js/main.js') ?>" defer></script>