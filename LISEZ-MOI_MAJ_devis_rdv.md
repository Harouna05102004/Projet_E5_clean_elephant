# Mise à jour : rendez-vous devis + espace client + admin

## Ce qui a changé

1. **Formulaire de devis (`devis.php`)** : il ne demande plus un devis direct, mais une **date et un créneau de rendez-vous**. À la validation, un **numéro de suivi** (`CE-XXXXXXXX`) est généré, affiché à l'écran et envoyé par email.
2. **Nouvel espace admin (`admin/`)** : protégé par mot de passe. Liste toutes les demandes, filtrables par statut, avec le détail de chaque demande pour :
   - changer son statut (nouvelle → rendez-vous confirmé → devis envoyé → accepté/refusé),
   - saisir un montant,
   - uploader le PDF du devis, puis plus tard celui de la facture.
3. **Nouvel espace client (`espace-client/`)** : le client entre son numéro de suivi, reçoit un **code à usage unique par email**, puis accède à une page montrant le statut de sa demande, le montant, et les PDF (devis/facture) à télécharger dès qu'ils sont disponibles.

## Mise en place

1. **Copie les fichiers** de ce zip dans ton dossier `Projet_E5_clean_elephant` (remplace les fichiers existants, les nouveaux dossiers `admin/` et `espace-client/` s'ajoutent).
2. **Base de données** :
   - Si tu repars d'une base vide : importe `schema.sql` comme avant.
   - Si tu as déjà une base `clean_elephant` avec des données : importe plutôt `migration_v2_rdv_suivi.sql` (il ajoute les nouvelles colonnes/tables sans rien supprimer).
3. **`env.php`** : ajoute une ligne `ADMIN_PASSWORD_HASH` (voir `env.example.php` pour le modèle). Génère ton hash avec :
   ```
   php -r "echo password_hash('ton-mot-de-passe', PASSWORD_DEFAULT) . PHP_EOL;"
   ```
   et colle le résultat dans `env.php` (jamais dans `env.example.php`, ni committé).
4. Vérifie que les dossiers `uploads/devis/` et `uploads/factures/` existent et sont accessibles en écriture par Apache (sous XAMPP ça fonctionne normalement sans rien à faire).
5. Accède à :
   - `http://localhost/Projet_E5_clean_elephant/devis.php` (formulaire client)
   - `http://localhost/Projet_E5_clean_elephant/admin/login.php` (admin)
   - `http://localhost/Projet_E5_clean_elephant/espace-client/index.php` (suivi client)

## Sécurité (déjà en place)

- Mot de passe admin jamais stocké en clair (hashé avec `password_hash`/`password_verify`).
- Connexion client sans mot de passe : numéro de suivi + code à usage unique envoyé par email, valable 10 minutes, à usage unique.
- Upload : seuls de vrais PDF sont acceptés (vérification du contenu réel du fichier, pas seulement de l'extension), taille limitée à 5 Mo, fichiers renommés (jamais le nom d'origine envoyé par le visiteur), et le dossier `uploads/` interdit l'exécution de PHP (`.htaccess`).
- Toutes les requêtes SQL restent en requêtes préparées (PDO).

## Testé

L'ensemble du parcours (demande → email de confirmation → connexion admin → changement de statut + upload PDF → connexion espace client par code → affichage du devis) a été testé de bout en bout sur un serveur PHP + MariaDB avant livraison.
