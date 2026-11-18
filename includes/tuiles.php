<?php
/*
 * includes/tuiles.php — Grille de tuiles de prestations
 * Partagé par particuliers.php et entreprises.php.
 *
 * La page doit définir, avant l'include, un tableau $prestations :
 *   [ 'cle' => 'maison', 'titre' => 'Maison', 'texte' => '...' ], ...
 * La clé sert à la fois pour l'icône et pour pré-cocher la case du devis
 * (devis.php?type=maison) : elle doit exister dans includes/referentiels.php.
 */
?>
<ul class="tiles">
    <?php foreach ($prestations as $rang => $prestation): ?>
        <li data-reveal>
            <!-- La première tuile est mise en avant (bleu nuit, plus large) -->
            <a class="tile<?= $rang === 0 ? ' tile--feature' : '' ?>" href="devis.php?type=<?= e($prestation['cle']) ?>">
                <span class="tile__icon"><?= icone($prestation['cle']) ?></span>
                <span class="tile__title"><?= e($prestation['titre']) ?></span>
                <span class="tile__text"><?= e($prestation['texte']) ?></span>
                <span class="tile__cta">Demander un devis <?= icone('fleche') ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>