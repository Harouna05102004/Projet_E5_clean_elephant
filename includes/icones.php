<?php
/*
 * includes/icones.php — Bibliothèque d'icônes SVG du site
 *
 * Toutes les icônes sont dessinées en SVG, directement dans la page :
 *   - aucune requête supplémentaire vers le serveur ;
 *   - elles prennent la couleur du texte (currentColor) et s'adaptent au CSS ;
 *   - aria-hidden="true" : décoratives, les lecteurs d'écran les ignorent.
 *
 * Utilisation : <?= icone('maison') ?>
 */

if (!function_exists('icone')) {
    function icone(string $nom): string
    {
        // Seulement le contenu de chaque dessin ; l'enveloppe <svg> est commune
        static $dessins = [
            'fleche'      => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'telephone'   => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
            'email'       => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/>',
            'horloge'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'repere'      => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
            'check'       => '<path d="M20 6 9 17l-5-5"/>',
            // Prestations
            'appartement' => '<rect x="4" y="2" width="16" height="20" rx="1"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
            'maison'      => '<path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
            'veranda'     => '<path d="M3 21V10l9-6 9 6v11z"/><path d="M3 15h18M9 21V8M15 21V8"/>',
            'vitrine'     => '<path d="M3 9h18l-1.5-5h-15z"/><path d="M4 9v12h16V9"/><path d="M9 21v-7h6v7"/>',
            'hauteur'     => '<path d="M8 3v18M16 3v18M8 8h8M8 13h8M8 18h8"/>',
            'facade'      => '<rect x="3" y="3" width="18" height="18" rx="1"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/>',
            'bureaux'     => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
            'immeuble'    => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18z"/><path d="M6 12H4a2 2 0 0 0-2 2v8h4M18 9h2a2 2 0 0 1 2 2v11h-4M10 6h4M10 10h4M10 14h4M10 18h4"/>',
            'copropriete' => '<path d="m2 11 5-4 5 4v10H2z"/><path d="m12 11 5-4 5 4v10H12z"/><path d="M5 21v-4h4v4M15 21v-4h4v4"/>',
            'contrat'     => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="m9 15 2 2 4-4"/>',
        ];

        if (!isset($dessins[$nom])) {
            return '';
        }
        // La flèche reçoit la classe icon-arrow : le CSS la fait glisser au survol des boutons
        $classe = $nom === 'fleche' ? ' class="icon-arrow"' : '';
        return '<svg' . $classe . ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
             . $dessins[$nom] . '</svg>';
    }
}