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
