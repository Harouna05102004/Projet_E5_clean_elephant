<?php
/*
 * espace-client/espace.php — Étape 3 : le suivi de la demande
 *
 * Nouveautés :
 *   - frise d'avancement (demande reçue -> rendez-vous -> devis -> décision) :
 *     le client voit d'un coup d'œil où en est sa demande ;
 *   - déconnexion automatique après 30 minutes d'inactivité ;
 *   - les PDF passent par telecharger.php, qui vérifie que le client est
 *     connecté et que le fichier lui appartient (avant : lien direct).
 */
require_once __DIR__ . '/../includes/securite.php';
require_once __DIR__ . '/../includes/icones.php';
require_once __DIR__ . '/../config.php';

$base        = '../';
$page_active = 'suivi';

const INACTIVITE_MAX = 30 * 60;   // 30 minutes, en secondes

if (empty($_SESSION['espace_client_devis_id'])) {
    header('Location: index.php');
    exit;
}

// Déconnexion automatique si le client est resté inactif trop longtemps
if (time() - ($_SESSION['espace_client_derniere_action'] ?? 0) > INACTIVITE_MAX) {
    unset($_SESSION['espace_client_devis_id'], $_SESSION['espace_client_derniere_action']);
    $_SESSION['ec_erreur'] = 'Votre session a expiré après 30 minutes d\'inactivité. Reconnectez-vous.';
    header('Location: index.php');
    exit;
}
$_SESSION['espace_client_derniere_action'] = time();

$stmt = $pdo->prepare('SELECT * FROM devis WHERE id = :id');
$stmt->execute([':id' => $_SESSION['espace_client_devis_id']]);
$demande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demande) {
    unset($_SESSION['espace_client_devis_id']);
    header('Location: index.php');
    exit;
}

/*
 * Frise d'avancement en 5 étapes. Chaque étape est « faite », « en cours » ou « à venir ».
 * $rang = numéro de l'étape en cours (0 à 4) ; 5 = tout est terminé.
 */
$ordre = ['nouvelle' => 0, 'rdv_confirme' => 1, 'devis_envoye' => 2, 'accepte' => 3, 'refuse' => 3, 'planifie' => 4, 'termine' => 5];
$rang  = $ordre[$demande['statut']] ?? 0;
$refuse = $demande['statut'] === 'refuse';

$dateInter = $demande['date_intervention'] ? date('d/m/Y', strtotime($demande['date_intervention'])) : '';
$quandInter = $dateInter . ($demande['heure_intervention'] ? ', ' . $demande['heure_intervention'] : '');

$etapes = [
    ['titre' => 'Demande reçue',        'texte' => 'Nous avons bien reçu votre demande.'],
    ['titre' => 'Rendez-vous confirmé', 'texte' => 'Notre technicien passe évaluer votre besoin.'],
    ['titre' => 'Devis envoyé',         'texte' => $rang === 2 ? 'Votre devis est prêt : donnez-nous votre réponse ci-contre.' : 'Votre devis est disponible ci-contre.'],
    $refuse
        ? ['titre' => 'Devis refusé',  'texte' => 'Une question ? Appelez-nous au 07 49 18 76 47.']
        : ['titre' => 'Devis accepté', 'texte' => $rang === 3 ? 'Merci ! Nous fixons la date de votre intervention.' : 'Votre accord est bien enregistré.'],
    ['titre' => $demande['statut'] === 'termine' ? 'Intervention réalisée' : 'Intervention',
     'texte' => $demande['statut'] === 'termine' ? 'Merci pour votre confiance !'
              : ($quandInter ? 'Prévue le ' . $quandInter . '.' : 'La date vous sera communiquée par e-mail.')],
];

// Le client peut répondre seulement si le devis est envoyé, avec un PDF, et pas encore de réponse
$peutRepondre = $demande['statut'] === 'devis_envoye' && !empty($demande['fichier_devis']);
$montantTxt = $demande['montant_devis'] !== null ? number_format((float) $demande['montant_devis'], 2, ',', ' ') . ' € TTC' : '';

// Message laissé par traitement_reponse.php (affiché une seule fois)
$flash = $_SESSION['ec_flash'] ?? null;
unset($_SESSION['ec_flash']);

$dateRdv = $demande['date_rdv'] ? date('d/m/Y', strtotime($demande['date_rdv'])) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon suivi | Clean Elephant</title>
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#0B1E3A">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main id="main-content">

    <section class="page-hero" aria-labelledby="titre-page">
        <div class="container">
            <div class="page-hero__grid">
                <div>
                    <p class="kicker">Espace client · <?= e($demande['numero_suivi']) ?></p>
                    <h1 class="page-hero__title" id="titre-page">
                        <span class="line"><span>Bonjour</span></span>
                        <span class="line"><span><em><?= e($demande['prenom']) ?>.</em></span></span>
                    </h1>
                </div>
                <div>
                    <p class="lead">Voici où en est votre demande. Cette page se met à jour à chaque étape.</p>
                    <a href="deconnexion.php" class="btn btn--ghost">Se déconnecter</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <?php if ($flash): ?>
            <div class="container">
                <div class="<?= $flash['type'] === 'succes' ? 'alert-success' : 'alert-error' ?> suivi__flash" role="status"><?= e($flash['message']) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($peutRepondre): ?>
            <!-- Réponse au devis : visible uniquement tant que le client n'a pas répondu -->
            <div class="container">
                <div class="form-card decision" id="reponse">
                    <div class="decision__intro">
                        <p class="kicker">Votre réponse</p>
                        <h2>Votre devis vous convient&#8239;?</h2>
                        <p>Relisez-le avec le lien « Mon devis », puis donnez-nous votre réponse ici. Elle nous parvient immédiatement.</p>
                    </div>

                    <form action="traitement_reponse.php" method="POST" class="decision__accept">
                        <?= csrf_champ() ?>
                        <input type="hidden" name="decision" value="accepte">
                        <label class="checkbox-item">
                            <input type="checkbox" name="confirmation" value="1" required>
                            <span>J'ai lu le devis <strong><?= e($demande['numero_suivi']) ?></strong><?= $montantTxt ? ' d\'un montant de <strong>' . e($montantTxt) . '</strong>' : '' ?> et je l'accepte.</span>
                        </label>
                        <button type="submit" class="btn btn-primary btn-block"><?= icone('check') ?> J'accepte le devis</button>
                        <p class="form-note">Votre accord est enregistré avec sa date et son heure. Nous vous contactons ensuite pour fixer l'intervention.</p>
                    </form>

                    <details class="decision__refuse">
                        <summary>Je ne souhaite pas donner suite</summary>
                        <form action="traitement_reponse.php" method="POST">
                            <?= csrf_champ() ?>
                            <input type="hidden" name="decision" value="refuse">
                            <div class="form-group">
                                <label for="motif">Pourquoi ? (facultatif)</label>
                                <textarea id="motif" name="motif" maxlength="500" rows="3" placeholder="Prix, délai, autre prestataire… Votre avis nous aide à nous améliorer."></textarea>
                            </div>
                            <button type="submit" class="btn btn-outline-dark">Je refuse le devis</button>
                        </form>
                        <p class="form-note form-note-inline">Une question avant de décider ? Appelez-nous au <a href="tel:+33749187647">07 49 18 76 47</a>.</p>
                    </details>
                </div>
            </div>
        <?php endif; ?>

        <div class="container suivi__grid">

            <!-- Frise d'avancement -->
            <div class="form-card">
                <h2>Avancement</h2>
                <ol class="timeline">
                    <?php foreach ($etapes as $i => $etape):
                        // Étape finie, en cours, ou à venir ? (une demande refusée est affichée en rouge)
                        if ($refuse) {
                            // Refus : les 3 premières étapes sont faites, la 4e en rouge, la 5e n'aura pas lieu
                            $etat = $i < 3 ? 'is-done' : ($i === 3 ? 'is-refused' : 'is-todo');
                        } elseif ($i < $rang) {
                            $etat = 'is-done';
                        } elseif ($i === $rang) {
                            $etat = 'is-current';
                        } else {
                            $etat = 'is-todo';
                        }
                    ?>
                        <li class="timeline__step <?= $etat ?>"<?= $etat === 'is-current' ? ' aria-current="step"' : '' ?>>
                            <span class="timeline__dot" aria-hidden="true"><?= $etat === 'is-done' ? icone('check') : ($etat === 'is-refused' ? '✕' : $i + 1) ?></span>
                            <div>
                                <strong><?= e($etape['titre']) ?></strong>
                                <p><?= e($etape['texte']) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>

            <div>
                <!-- Documents -->
                <div class="form-card">
                    <h2>Mes documents</h2>
                    <?php if ($demande['fichier_devis'] || $demande['fichier_facture']): ?>
                        <ul class="doc-list">
                            <?php if ($demande['fichier_devis']): ?>
                                <li>
                                    <a class="doc" href="telecharger.php?doc=devis">
                                        <span class="contact-card__icon"><?= icone('contrat') ?></span>
                                        <span><strong>Mon devis</strong><small>PDF · à télécharger</small></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($demande['fichier_facture']): ?>
                                <li>
                                    <a class="doc" href="telecharger.php?doc=facture">
                                        <span class="contact-card__icon"><?= icone('contrat') ?></span>
                                        <span><strong>Ma facture</strong><small>PDF · à télécharger</small></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php else: ?>
                        <p class="form-note form-note-inline">Votre devis apparaîtra ici dès que notre technicien l'aura établi, après le rendez-vous.</p>
                    <?php endif; ?>
                </div>

                <!-- Récapitulatif -->
                <div class="form-card recap">
                    <h2>Récapitulatif</h2>
                    <dl class="recap__list">
                        <dt>Rendez-vous souhaité</dt>
                        <dd><?= $dateRdv ? e($dateRdv) . ($demande['heure_rdv'] ? ', ' . e($demande['heure_rdv']) : '') : 'Non précisé' ?></dd>

                        <?php if (!empty($demande['type_nettoyage'])): ?>
                            <dt>Prestations</dt>
                            <dd><?= e($demande['type_nettoyage']) ?></dd>
                        <?php endif; ?>

                        <?php if ($demande['montant_devis'] !== null): ?>
                            <dt>Montant du devis</dt>
                            <dd><?= e(number_format((float) $demande['montant_devis'], 2, ',', ' ')) ?> €</dd>
                        <?php endif; ?>

                        <?php if (!empty($demande['date_reponse'])): ?>
                            <dt>Votre réponse</dt>
                            <dd><?= $refuse ? 'Refusé' : 'Accepté' ?> le <?= e(date('d/m/Y à H:i', strtotime($demande['date_reponse']))) ?></dd>
                        <?php endif; ?>

                        <?php if ($quandInter && !$refuse): ?>
                            <dt>Intervention</dt>
                            <dd class="recap__highlight"><?= e($quandInter) ?></dd>
                        <?php endif; ?>
                    </dl>
                    <a class="aside-phone" href="tel:+33749187647">
                        <?= icone('telephone') ?>
                        <span><small>Une question sur votre demande ?</small><strong>07 49 18 76 47</strong></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>