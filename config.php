<?php
/*
 * config.php — Connexion à la base de données Clean Elephant (PDO)
 *
 * Changements :
 *   - charset utf8mb4 (comme dans schema.sql) : le "utf8" de MySQL est incomplet ;
 *   - en cas d'échec, le détail va dans le journal du serveur et le visiteur
 *     ne voit qu'un message neutre (avant, il voyait le nom d'hôte, l'utilisateur...).
 */
require_once __DIR__ . '/env.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS
    );
    // Toute erreur SQL lève une exception qu'on peut attraper avec try/catch
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log('Connexion BDD impossible : ' . $e->getMessage());
    http_response_code(503);   // 503 = service indisponible
    exit('Le service est momentanément indisponible. Merci de réessayer plus tard.');
}