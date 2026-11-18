<?php
/*
 * admin/messages.php : demandes de rappel et messages de contact
 *
 * Avant, ces formulaires remplissaient la base sans que personne ne le sache.
 * Ici, l'entreprise voit ce qui reste à faire et le marque « traité ».
 * RGPD : à l'ouverture de la page, ce qui a été traité il y a plus d'un an est
 * supprimé (limitation de la durée de conservation : on ne garde pas des
 * numéros de téléphone dont on n'a plus besoin).
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

const CONSERVATION_APRES_TRAITEMENT = '1 YEAR';
$pdo->exec('DELETE FROM rappels  WHERE traite_le < NOW() - INTERVAL ' . CONSERVATION_APRES_TRAITEMENT);
$pdo->exec('DELETE FROM contacts WHERE traite_le < NOW() - INTERVAL ' . CONSERVATION_APRES_TRAITEMENT);

// Onglet affiché : une valeur de notre liste, sinon « rappels »
$onglet = ($_GET['onglet'] ?? '') === 'contacts' ? 'contacts' : 'rappels';
$vue    = ($_GET['vue'] ?? '') === 'traites' ? 'traites' : 'a_traiter';

// Nom de table tiré de notre liste (jamais de l'URL) : sûr dans la requête
$table = $onglet === 'contacts' ? 'contacts' : 'rappels';
$condition = $vue === 'traites' ? 'traite_le IS NOT NULL' : 'traite_le IS NULL';
$ordre = $vue === 'traites' ? 'traite_le DESC' : 'date_creation ASC';   // à traiter : le plus ancien en premier
$lignes = $pdo->query("SELECT * FROM $table WHERE $condition ORDER BY $ordre")->fetchAll(PDO::FETCH_ASSOC);

$nbRappels  = (int) $pdo->query('SELECT COUNT(*) FROM rappels  WHERE traite_le IS NULL')->fetchColumn();
$nbContacts = (int) $pdo->query('SELECT COUNT(*) FROM contacts WHERE traite_le IS NULL')->fetchColumn();

$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);

/** Lien vers un onglet / une vue de cette page */
function lien(string $onglet, string $vue = 'a_traiter'): string
{
    return 'messages.php?' . http_build_query(['onglet' => $onglet, 'vue' => $vue]);
}

$titre_page = 'Rappels et messages';
$page_admin = 'messages';
require __DIR__ . '/includes/entete.php';
?>

<div class="adm-head">
    <div>
        <p class="adm-kicker">À traiter</p>
        <h1>Rappels et messages</h1>
    </div>
</div>

<?php if ($flash): ?>
    <div class="<?= $flash['type'] === 'succes' ? 'alert-success' : 'alert-error' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>

<nav class="adm-tabs" aria-label="Type de demande">
    <a href="<?= e(lien('rappels')) ?>" class="adm-tab<?= $onglet === 'rappels' ? ' is-active' : '' ?>">
        Demandes de rappel <span class="adm-tab__n"><?= $nbRappels ?></span>
    </a>
    <a href="<?= e(lien('contacts')) ?>" class="adm-tab<?= $onglet === 'contacts' ? ' is-active' : '' ?>">
        Messages de contact <span class="adm-tab__n"><?= $nbContacts ?></span>
    </a>
</nav>

<p class="adm-subnav">
    <a href="<?= e(lien($onglet)) ?>" class="<?= $vue === 'a_traiter' ? 'is-active' : '' ?>">À traiter</a>
    <a href="<?= e(lien($onglet, 'traites')) ?>" class="<?= $vue === 'traites' ? 'is-active' : '' ?>">Déjà traités</a>
    <span class="adm-muted">Les demandes traitées sont supprimées automatiquement au bout d'un an.</span>
</p>

<?php if (!$lignes): ?>
    <div class="adm-empty">
        <p><strong><?= $vue === 'a_traiter' ? 'Tout est traité.' : 'Rien ici pour l\'instant.' ?></strong></p>
        <p><?= $onglet === 'rappels' ? 'Les demandes « Être rappelé gratuitement » du site arrivent ici.' : 'Les messages du formulaire de contact arrivent ici.' ?></p>
    </div>
<?php else: ?>
    <ul class="adm-msg-list">
        <?php foreach ($lignes as $l): ?>
            <li class="adm-msg<?= $l['traite_le'] ? ' is-done' : '' ?>">
                <div class="adm-msg__main">
                    <p class="adm-msg__who">
                        <strong><?= e($l['prenom'] . ' ' . $l['nom']) ?></strong>
                        <span class="adm-muted">reçu le <?= e(date('d/m/Y à H:i', strtotime($l['date_creation']))) ?></span>
                    </p>
                    <p class="adm-msg__contact">
                        <?php if (!empty($l['telephone'])): ?>
                            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) $l['telephone'])) ?>" class="adm-msg__tel"><?= e($l['telephone']) ?></a>
                        <?php endif; ?>
                        <?php if (!empty($l['email'])): ?>
                            <a href="mailto:<?= e($l['email']) ?>"><?= e($l['email']) ?></a>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($l['message'])): ?>
                        <p class="adm-msg__text"><?= nl2br(e($l['message'])) ?></p>
                    <?php endif; ?>
                    <?php if ($l['traite_le']): ?>
                        <p class="adm-muted">Traité le <?= e(date('d/m/Y à H:i', strtotime($l['traite_le']))) ?></p>
                    <?php endif; ?>
                </div>
                <form action="traitement_message.php" method="POST" class="adm-msg__action">
                    <?= csrf_champ() ?>
                    <input type="hidden" name="type" value="<?= e($onglet) ?>">
                    <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                    <?php if ($l['traite_le']): ?>
                        <input type="hidden" name="action" value="rouvrir">
                        <button type="submit" class="btn btn-outline-dark adm-btn-sm">Remettre à traiter</button>
                    <?php else: ?>
                        <input type="hidden" name="action" value="traiter">
                        <button type="submit" class="btn btn-primary adm-btn-sm">✓ Traité</button>
                    <?php endif; ?>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require __DIR__ . '/includes/pied.php'; ?>