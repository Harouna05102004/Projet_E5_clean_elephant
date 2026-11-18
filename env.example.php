<?php
// Modèle de configuration locale — copie ce fichier en "env.php" et remplis tes vraies valeurs.
// env.php est ignoré par git (.gitignore) : il ne sera jamais poussé sur GitHub.

// Base de données (MySQL / XAMPP)
define('DB_HOST', 'localhost');
define('DB_NAME', 'clean_elephant');
define('DB_USER', 'root');
define('DB_PASS', '');

// SMTP (sandbox Mailtrap - environnement de test E5)
define('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
define('SMTP_USERNAME', 'ton-username-mailtrap');
define('SMTP_PASSWORD', 'ton-mot-de-passe-mailtrap');
define('SMTP_PORT', 2525);

// Adresse du service commercial, qui reçoit une notification à chaque nouvelle demande
define('COMMERCIAL_EMAIL', 'commercial@cleanelephant.fr');

// Mot de passe de l'espace admin (jamais en clair : on stocke son hash).
// Pour générer le hash de ton mot de passe, exécute en ligne de commande :
//   php -r "echo password_hash('ton-mot-de-passe', PASSWORD_DEFAULT) . PHP_EOL;"
// puis colle le résultat ci-dessous (remplace tout, y compris les $ qui font partie du hash).
define('ADMIN_PASSWORD_HASH', '$2y$10$remplaceCeHashParLeTienAvecLaCommandeCiDessus');

// (Production seulement) Adresse publique du site, utilisée dans les liens des e-mails.
// En local, laisse cette ligne en commentaire : l'adresse est devinée automatiquement.
// define('SITE_URL', 'https://www.cleanelephant.fr/');