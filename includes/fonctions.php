<?php
// Fonctions utilitaires partagées (demande de devis, suivi, admin)

/**
 * Génère un numéro de suivi unique au format CE-XXXXXXXX (8 caractères hexadécimaux).
 * Vérifie en base qu'il n'existe pas déjà avant de le renvoyer.
 */
function genererNumeroSuivi(PDO $pdo): string {
    do {
        $numero = 'CE-' . strtoupper(bin2hex(random_bytes(4)));
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM devis WHERE numero_suivi = :numero');
        $stmt->execute([':numero' => $numero]);
        $existeDeja = $stmt->fetchColumn() > 0;
    } while ($existeDeja);

    return $numero;
}

/**
 * Génère un code de vérification à usage unique à 6 chiffres (OTP),
 * envoyé par email pour la connexion à l'espace client.
 */
function genererCodeOTP(): string {
    return (string) random_int(100000, 999999);
}

/**
 * Libellés affichables pour chaque statut d'une demande de devis.
 */
function libelleStatut(string $statut): string {
    $libelles = [
        'nouvelle'      => 'Nouvelle demande',
        'rdv_confirme'  => 'Rendez-vous confirmé',
        'devis_envoye'  => 'Devis envoyé',
        'accepte'       => 'Devis accepté',
        'refuse'        => 'Devis refusé',
    ];
    return $libelles[$statut] ?? $statut;
}

/**
 * Classe CSS (badge coloré) associée à chaque statut.
 */
function classeStatut(string $statut): string {
    $classes = [
        'nouvelle'      => 'badge-nouvelle',
        'rdv_confirme'  => 'badge-rdv',
        'devis_envoye'  => 'badge-devis',
        'accepte'       => 'badge-accepte',
        'refuse'        => 'badge-refuse',
    ];
    return $classes[$statut] ?? 'badge-nouvelle';
}
?>
