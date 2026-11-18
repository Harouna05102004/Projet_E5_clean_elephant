<?php
/*
 * admin/devis.php — Fiche d'une demande
 * Nouveautés : mise en page en deux colonnes, frise d'avancement, liens tel/mailto,
 * formulaire protégé par jeton CSRF, PDF ouverts via fichier.php (contrôle d'accès)
 * au lieu d'un lien direct vers uploads/.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/referentiels.php';   // CRENEAUX

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;

$stmt = $pdo->prepare('SELECT * FROM devis WHERE id = :id');
$stmt->execute([':id' => $id]);
$demande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demande) {
    header('Location: index.php');
    exit;
}

// Message laissé par traitement_update.php (lu une fois puis effacé)
$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);

// Étapes de la frise. $niveau = étape en cours (5 = tout est terminé).
$etapes = ['Demande reçue', 'Rendez-vous confirmé', 'Devis envoyé', 'Réponse du client', 'Intervention'];
$ordre  = ['nouvelle' => 0, 'rdv_confirme' => 1, 'devis_envoye' => 2, 'accepte' => 3, 'refuse' => 3, 'planifie' => 4, 'termine' => 5];
$niveau = $ordre[$demande['statut']] ?? 0;
$refuse = $demande['statut'] === 'refuse';
if (in_array($demande['statut'], ['accepte', 'planifie', 'termine'], true)) {
    $etapes[3] = 'Devis accepté';
} elseif ($refuse) {
    $etapes[3] = 'Devis refusé';
}
if ($demande['statut'] === 'termine') {
    $etapes[4] = 'Intervention réalisée';
} elseif ($demande['date_intervention']) {
    $etapes[4] = 'Intervention le ' . date('d/m', strtotime($demande['date_intervention']));
}

$titre_page = $demande['numero_suivi'];
require __DIR__ . '/includes/entete.php';
?>

<p class="adm-back"><a href="index.php">&larr; Toutes les demandes</a></p>

<div class="adm-head">
    <div>
        <p class="adm-kicker">Demande <code class="adm-code"><?= e($demande['numero_suivi']) ?></code></p>
        <h1><?= e($demande['prenom'] . ' ' . $demande['nom']) ?></h1>
        <p class="adm-muted">Reçue le <?= e(date('d/m/Y à H:i', strtotime($demande['date_creation']))) ?></p>
    </div>
    <?= admin_badge($demande['statut']) ?>
</div>

<?php if ($flash): ?>
    <div class="<?= $flash['type'] === 'succes' ? 'alert-success' : 'alert-error' ?>" role="status">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<ol class="adm-steps" aria-label="Avancement de la demande">
    <?php foreach ($etapes as $i => $libelle):
        if ($refuse) {
            $etat = $i < 3 ? 'fait' : ($i === 3 ? 'refuse' : 'avenir');
        } else {
            $etat = $i < $niveau ? 'fait' : ($i === $niveau ? 'actuel' : 'avenir');
        }
    ?>
        <li class="adm-step adm-step--<?= $etat ?>"><span class="adm-step__dot"></span><?= e($libelle) ?></li>
    <?php endforeach; ?>
</ol>

<?php if (!empty($demande['date_reponse'])): ?>
    <div class="adm-reponse adm-reponse--<?= $refuse ? 'non' : 'oui' ?>">
        <strong><?= $refuse ? 'Devis refusé en ligne' : 'Devis accepté en ligne' ?></strong>
        le <?= e(date('d/m/Y à H:i', strtotime($demande['date_reponse']))) ?>
        <span class="adm-muted">Adresse IP : <?= e($demande['ip_reponse'] ?? 'inconnue') ?> (preuve de la réponse)</span>
        <?php if ($demande['motif_refus']): ?>
            <p>Raison indiquée : « <?= nl2br(e($demande['motif_refus'])) ?> »</p>
        <?php endif; ?>
    </div>
<?php elseif ($demande['statut'] === 'accepte'): ?>
    <div class="adm-reponse adm-reponse--oui"><strong>Devis accepté</strong> (statut changé depuis l'admin, sans réponse en ligne).</div>
<?php endif; ?>

<?php if ($demande['statut'] === 'accepte'): ?>
    <div class="adm-todo" role="status">À faire : fixer la date de l'intervention dans le formulaire ci-dessous. Le client sera prévenu par e-mail.</div>
<?php endif; ?>

<div class="adm-grid">
    <section class="adm-card">
        <h2>Client et besoin</h2>
        <dl class="adm-fiche">
            <div><dt>Téléphone</dt><dd><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', (string) $demande['telephone'])) ?>"><?= e($demande['telephone']) ?></a></dd></div>
            <div><dt>E-mail</dt><dd><a href="mailto:<?= e($demande['email']) ?>"><?= e($demande['email']) ?></a></dd></div>
            <div><dt>Adresse</dt><dd><?= e(trim(($demande['adresse'] ?? '') . ', ' . ($demande['ville'] ?? ''), ', ') ?: 'Non précisée') ?></dd></div>
            <div><dt>Prestations</dt><dd><?= e($demande['type_nettoyage'] ?: 'Non précisé') ?></dd></div>
            <div><dt>Rendez-vous souhaité</dt>
                <dd>
                    <?php if ($demande['date_rdv']): ?>
                        <?= e(date('d/m/Y', strtotime($demande['date_rdv']))) ?><?= $demande['heure_rdv'] ? ' · ' . e($demande['heure_rdv']) : '' ?>
                    <?php else: ?>Non précisé<?php endif; ?>
                </dd>
            </div>
            <div class="adm-fiche__full"><dt>Message</dt><dd><?= nl2br(e($demande['message'] ?: 'Aucun message.')) ?></dd></div>
        </dl>
    </section>

    <section class="adm-card adm-card--form">
        <h2>Traiter la demande</h2>
        <form action="traitement_update.php" method="POST" enctype="multipart/form-data">
            <?= csrf_champ() ?>
            <input type="hidden" name="id" value="<?= (int) $demande['id'] ?>">

            <div class="form-group">
                <label for="statut">Statut</label>
                <select id="statut" name="statut">
                    <?php foreach (ADMIN_STATUTS as $cle => $libelle): ?>
                        <option value="<?= e($cle) ?>" <?= $demande['statut'] === $cle ? 'selected' : '' ?>><?= e($libelle) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="montant_devis">Montant du devis (€ TTC)</label>
                <input type="number" step="0.01" min="0" max="100000" id="montant_devis" name="montant_devis" inputmode="decimal"
                       value="<?= e((string) ($demande['montant_devis'] ?? '')) ?>">
            </div>

            <fieldset class="adm-fieldset">
                <legend>Intervention</legend>
                <div class="adm-row">
                    <div class="form-group">
                        <label for="date_intervention">Date</label>
                        <input type="date" id="date_intervention" name="date_intervention"
                               value="<?= e($demande['date_intervention'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="heure_intervention">Créneau</label>
                        <select id="heure_intervention" name="heure_intervention">
                            <option value="">Choisir</option>
                            <?php foreach (CRENEAUX as $c): ?>
                                <option value="<?= e($c) ?>" <?= ($demande['heure_intervention'] ?? '') === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <p class="adm-hint">Une date sur un devis accepté passe la demande en « Intervention planifiée » et prévient le client par e-mail.</p>
            </fieldset>

            <?php foreach (['devis' => 'Devis', 'facture' => 'Facture'] as $doc => $libelle): ?>
                <div class="form-group">
                    <label for="fichier_<?= $doc ?>"><?= $libelle ?> (PDF, 5 Mo max)</label>
                    <input type="file" id="fichier_<?= $doc ?>" name="fichier_<?= $doc ?>" accept="application/pdf" class="adm-file">
                    <?php if (!empty($demande['fichier_' . $doc])): ?>
                        <p class="adm-file-current">
                            Fichier actuel : <a href="fichier.php?id=<?= (int) $demande['id'] ?>&amp;doc=<?= $doc ?>" target="_blank" rel="noopener">ouvrir le PDF</a>
                            <span class="adm-muted">· un nouvel envoi le remplace</span>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
        </form>
    </section>
</div>

<?php require __DIR__ . '/includes/pied.php'; ?>