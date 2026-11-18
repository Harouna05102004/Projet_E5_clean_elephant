<?php
/*
 * includes/securite.php — Boîte à outils de sécurité de Clean Elephant
 *
 * Inclus EN PREMIER (avant tout affichage HTML) par chaque page qui affiche
 * ou traite un formulaire. Il fournit :
 *   - la session PHP, configurée de façon sûre ;
 *   - e()               : échapper un texte au moment de l'AFFICHER (anti-XSS) ;
 *   - csrf_champ()      : le champ caché à mettre dans chaque formulaire ;
 *   - csrf_verifier()   : la vérification de ce jeton à la réception ;
 *   - est_un_robot()    : le piège anti-robots (honeypot) ;
 *   - lire_texte() / lire_liste() : lire $_POST sans se faire surprendre par un type inattendu.
 */

// ---------------------------------------------------------------------------
// 1. Session
// La session sert à garder des infos d'une page à l'autre pour UN visiteur
// (jeton CSRF, messages d'erreur...). Le navigateur ne garde qu'un identifiant
// dans un cookie ; les données restent sur le serveur.
// ---------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,                       // cookie supprimé à la fermeture du navigateur
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']), // envoyé uniquement en HTTPS quand le site est en HTTPS
        'httponly' => true,                    // illisible par JavaScript -> un XSS ne peut pas voler la session
        'samesite' => 'Lax',                   // pas envoyé par les formulaires d'autres sites -> aide contre le CSRF
    ]);
    session_start();
}

// ---------------------------------------------------------------------------
// 2. Échappement en sortie
// Règle : on stocke la donnée telle quelle, et on l'échappe AU MOMENT de
// l'afficher dans du HTML. "<script>" devient "&lt;script&gt;" : le navigateur
// l'affiche comme du texte au lieu de l'exécuter.
// ---------------------------------------------------------------------------
function e(?string $texte): string
{
    return htmlspecialchars($texte ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ---------------------------------------------------------------------------
// 3. Jeton CSRF
// Problème : un autre site peut contenir un formulaire caché qui POSTe vers
// notre traitement_devis.php. Solution : chaque formulaire légitime contient
// un jeton secret aléatoire, stocké aussi dans la session. Un site pirate ne
// peut pas le deviner, donc sa requête est refusée.
// ---------------------------------------------------------------------------
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        // random_bytes = générateur cryptographiquement sûr (contrairement à rand())
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_champ(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verifier(): bool
{
    $recu   = $_POST['csrf_token'] ?? '';
    $attendu = $_SESSION['csrf_token'] ?? '';
    // hash_equals compare en temps constant (évite de deviner le jeton caractère par caractère)
    return is_string($recu) && $attendu !== '' && hash_equals($attendu, $recu);
}

// ---------------------------------------------------------------------------
// 4. Honeypot (pot de miel)
// Un champ "site_web" est caché aux humains par le CSS. Les robots de spam
// remplissent tous les champs qu'ils trouvent : s'il est rempli, c'est un robot.
// ---------------------------------------------------------------------------
function honeypot_champ(): string
{
    return '<div style="position:absolute;left:-9999px;" aria-hidden="true">'
         . '<label for="site_web">Ne pas remplir</label>'
         . '<input type="text" id="site_web" name="site_web" tabindex="-1" autocomplete="off">'
         . '</div>';
}

function est_un_robot(): bool
{
    return !empty($_POST['site_web']);
}

// ---------------------------------------------------------------------------
// 5. Lecture sûre de $_POST
// Un attaquant peut envoyer nom[]=x (un tableau) au lieu d'un texte : trim()
// planterait. Ces fonctions renvoient toujours le type attendu.
// ---------------------------------------------------------------------------
function lire_texte(string $nom): string
{
    $valeur = $_POST[$nom] ?? '';
    return is_string($valeur) ? trim($valeur) : '';
}

function lire_liste(string $nom): array
{
    $valeur = $_POST[$nom] ?? [];
    if (!is_array($valeur)) {
        return [];
    }
    return array_values(array_filter($valeur, 'is_string'));
}