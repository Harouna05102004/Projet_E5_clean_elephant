<?php
/*
 * admin/index.php — Liste des demandes de devis
 * Nouveautés : chiffres clés en haut, filtres en onglets, recherche (nom, e-mail,
 * téléphone ou numéro de suivi), tableau qui devient des cartes sur téléphone,
 * et tout ce qui vient de la base est échappé avec e() avant affichage.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';

// --- Filtre par statut : accepté seulement s'il est dans notre liste --------
$filtre = $_GET['statut'] ?? '';
if (!is_string($filtre) || !array_key_exists($filtre, ADMIN_STATUTS)) {
    $filtre = '';
}

// --- Recherche : texte libre, limité à 60 caractères -------------------------
$recherche = trim(is_string($_GET['q'] ?? null) ? $_GET['q'] : '');
$recherche = mb_substr($recherche, 0, 60);

// --- Requête construite par morceaux, valeurs toujours passées en paramètres --
$conditions = [];
$params = [];
if ($filtre !== '') {
    $conditions[] = 'statut = :statut';
    $params[':statut'] = $filtre;
}
if ($recherche !== '') {
    $conditions[] = '(nom LIKE :q1 OR prenom LIKE :q2 OR email LIKE :q3 OR telephone LIKE :q4 OR numero_suivi LIKE :q5)';
    // addcslashes : un « % » ou « _ » tapé par l'admin est cherché tel quel, pas comme joker SQL
    $motif = '%' . addcslashes($recherche, '%_\\') . '%';
    foreach (['q1', 'q2', 'q3', 'q4', 'q5'] as $k) {
        $params[':' . $k] = $motif;
    }
}
$sql = 'SELECT * FROM devis' . ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : '') . ' ORDER BY date_creation DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Compteurs ---------------------------------------------------------------
$compteurs = $pdo->query('SELECT statut, COUNT(*) FROM devis GROUP BY statut')->fetchAll(PDO::FETCH_KEY_PAIR);
$total = array_sum($compteurs);
$rdv_a_venir = (int) $pdo->query("SELECT COUNT(*) FROM devis WHERE date_rdv >= CURDATE() AND statut IN ('nouvelle','rdv_confirme')")->fetchColumn();
$a_planifier = (int) ($compteurs['accepte'] ?? 0);   // acceptés en attente d'une date d'intervention
$inter_a_venir = (int) $pdo->query("SELECT COUNT(*) FROM devis WHERE statut = 'planifie' AND date_intervention >= CURDATE()")->fetchColumn();
$cette_semaine = (int) $pdo->query('SELECT COUNT(*) FROM devis WHERE date_creation >= NOW() - INTERVAL 7 DAY')->fetchColumn();

/** Lien de filtre qui garde la recherche en cours */
function lien_filtre(string $statut, string $recherche): string
{
    $q = array_filter(['statut' => $statut, 'q' => $recherche], fn($v) => $v !== '');
    return 'index.php' . ($q ? '?' . http_build_query($q) : '');
}

$titre_page = 'Demandes de devis';
require __DIR__ . '/includes/entete.php';
?>

<div class="adm-head">
    <div>
        <p class="adm-kicker">Tableau de bord</p>
        <h1>Demandes de devis</h1>
    </div>
    <form class="adm-search" method="GET" action="index.php" role="search">
        <?php if ($filtre !== ''): ?><input type="hidden" name="statut" value="<?= e($filtre) ?>"><?php endif; ?>
        <label for="q" class="visually-hidden">Rechercher une demande</label>
        <input type="search" id="q" name="q" value="<?= e($recherche) ?>" placeholder="Nom, e-mail, tél. ou n° de suivi" maxlength="60">
        <button type="submit" class="btn btn-primary adm-btn-sm">Rechercher</button>
    </form>
</div>

<section class="adm-stats" aria-label="Chiffres clés">
    <div class="adm-stat adm-stat--accent">
        <span class="adm-stat__val"><?= (int) ($compteurs['nouvelle'] ?? 0) ?></span>
        <span class="adm-stat__lbl">nouvelles demandes à traiter</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat__val"><?= $rdv_a_venir ?></span>
        <span class="adm-stat__lbl">rendez-vous à venir</span>
    </div>
    <div class="adm-stat<?= $a_planifier ? ' adm-stat--alert' : '' ?>">
        <span class="adm-stat__val"><?= $a_planifier ?></span>
        <span class="adm-stat__lbl">devis acceptés à planifier</span>
    </div>
    <div class="adm-stat">
        <span class="adm-stat__val"><?= $inter_a_venir ?></span>
        <span class="adm-stat__lbl">interventions à venir</span>
    </div>
</section>

<nav class="adm-tabs" aria-label="Filtrer par statut">
    <a href="<?= e(lien_filtre('', $recherche)) ?>" class="adm-tab<?= $filtre === '' ? ' is-active' : '' ?>" <?= $filtre === '' ? 'aria-current="page"' : '' ?>>
        Toutes <span class="adm-tab__n"><?= (int) $total ?></span>
    </a>
    <?php foreach (ADMIN_STATUTS as $cle => $libelle): ?>
        <a href="<?= e(lien_filtre($cle, $recherche)) ?>" class="adm-tab adm-tab--<?= $cle ?><?= $filtre === $cle ? ' is-active' : '' ?>" <?= $filtre === $cle ? 'aria-current="page"' : '' ?>>
            <?= e($libelle) ?> <span class="adm-tab__n"><?= (int) ($compteurs[$cle] ?? 0) ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<?php if ($recherche !== ''): ?>
    <p class="adm-result">
        <?= count($demandes) ?> résultat<?= count($demandes) > 1 ? 's' : '' ?> pour « <?= e($recherche) ?> »
        · <a href="<?= e(lien_filtre($filtre, '')) ?>">effacer la recherche</a>
    </p>
<?php endif; ?>

<?php if (empty($demandes)): ?>
    <div class="adm-empty">
        <p><strong>Aucune demande ici.</strong></p>
        <p>Les nouvelles demandes envoyées depuis le site apparaîtront dans cette liste.</p>
    </div>
<?php else: ?>
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th scope="col">N° de suivi</th>
                    <th scope="col">Client</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Prochain rendez-vous</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Reçue le</th>
                    <th scope="col"><span class="visually-hidden">Action</span></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($demandes as $d): ?>
                <tr class="<?= in_array($d['statut'], ['nouvelle', 'accepte'], true) ? 'is-new' : '' ?>">
                    <td data-label="N° de suivi"><code class="adm-code"><?= e($d['numero_suivi']) ?></code></td>
                    <td data-label="Client"><strong><?= e($d['prenom'] . ' ' . $d['nom']) ?></strong></td>
                    <td data-label="Contact" class="adm-contact"><div>
                        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) $d['telephone'])) ?>"><?= e($d['telephone']) ?></a>
                        <a href="mailto:<?= e($d['email']) ?>"><?= e($d['email']) ?></a>
                    </div></td>
                    <td data-label="Rendez-vous"><div>
                        <?php if ($d['date_intervention'] && in_array($d['statut'], ['planifie', 'termine'], true)): ?>
                            <span class="adm-tag">Intervention</span>
                            <?= e(date('d/m/Y', strtotime($d['date_intervention']))) ?>
                            <?php if ($d['heure_intervention']): ?><span class="adm-muted"><?= e($d['heure_intervention']) ?></span><?php endif; ?>
                        <?php elseif ($d['date_rdv']): ?>
                            <?= e(date('d/m/Y', strtotime($d['date_rdv']))) ?>
                            <?php if ($d['heure_rdv']): ?><span class="adm-muted"><?= e($d['heure_rdv']) ?></span><?php endif; ?>
                        <?php else: ?>
                            <span class="adm-muted">Non précisé</span>
                        <?php endif; ?>
                    </div></td>
                    <td data-label="Statut"><?= admin_badge($d['statut']) ?></td>
                    <td data-label="Reçue le"><?= e(date('d/m/Y H:i', strtotime($d['date_creation']))) ?></td>
                    <td class="adm-cell-action">
                        <a href="devis.php?id=<?= (int) $d['id'] ?>" class="btn btn-outline-dark adm-btn-sm">Ouvrir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/pied.php'; ?>