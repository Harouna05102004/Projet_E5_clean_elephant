# Clean Elephant — Site vitrine

Site vitrine avec devis en ligne pour **Clean Elephant**, entreprise de nettoyage de vitres basée à Montmagny (Île-de-France).

Projet réalisé dans le cadre de l'épreuve **E5 du BTS SIO** (option SISR) à Efrei Paris, en binôme :
- **Harouna Diakité**
- **Noah Fine**

## Fonctionnalités

- Pages de présentation (accueil, particuliers, entreprises)
- Formulaire de demande de devis en ligne, avec email de confirmation automatique (PHPMailer + Mailtrap)
- Formulaire de contact
- Formulaire "rappel gratuit" présent dans le footer sur toutes les pages
- Enregistrement des demandes en base de données (MySQL, requêtes préparées)
- Validation des champs Nom/Prénom (saisie de chiffres bloquée, côté JS et HTML)
- Notification desktop pour le bouton d'appel (le lien `tel:` ouvre le composeur sur mobile ; sur desktop, le numéro est copié et affiché dans une notification)

## Stack technique

| Composant     | Choix                              |
|---------------|-------------------------------------|
| Frontend      | HTML5, CSS3, JavaScript vanilla     |
| Backend       | PHP 8 (PDO, requêtes préparées)     |
| Base de données | MySQL (via XAMPP)                 |
| Emailing      | PHPMailer + Mailtrap (sandbox de test) |
| Serveur local | Apache (XAMPP)                      |

## Arborescence

```
clean-elephant/
├── index.php                  Accueil
├── particuliers.php            Page particuliers
├── entreprises.php             Page entreprises
├── devis.php                   Formulaire de devis
├── contact.php                 Formulaire de contact
├── config.php                  Connexion PDO à la base de données
├── mailer.php                  Envoi de l'email de confirmation (PHPMailer)
├── env.example.php             Modèle de configuration locale (à copier en env.php)
├── traitement_devis.php        Traitement du formulaire de devis
├── traitement_contact.php      Traitement du formulaire de contact
├── traitement_rappel.php       Traitement du formulaire de rappel
├── schema.sql                  Structure de la base de données
├── css/style.css                Feuille de style
├── js/call-link.js             Fallback desktop pour le bouton d'appel
├── js/form-validation.js       Blocage des chiffres dans les champs nom/prénom
├── includes/header.php         En-tête commun à toutes les pages
├── includes/footer.php         Pied de page + formulaire de rappel
├── images/                     Visuels du site
└── lib/PHPMailer/              Librairie PHPMailer
```

## Installation (environnement local XAMPP)

1. Cloner ce repo dans le dossier `htdocs` de XAMPP :
   ```
   cd C:\xampp\htdocs
   git clone https://github.com/Harouna05102004/Projet_E5_clean_elephant.git
   ```
2. Démarrer **Apache** et **MySQL** depuis le panneau de contrôle XAMPP.
3. Créer la base de données en important `schema.sql` (via phpMyAdmin ou en ligne de commande).
4. Copier `env.example.php` en `env.php` et renseigner tes identifiants (base de données + SMTP Mailtrap) :
   ```
   copy env.example.php env.php
   ```
5. Accéder au site via `http://localhost/Projet_E5_clean_elephant/index.php`.

> ⚠️ `env.php` contient des identifiants et n'est jamais versionné (voir `.gitignore`). Chaque personne qui clone le repo doit créer son propre `env.php` à partir du modèle.

## Contexte projet

Ce dépôt correspond au **Livrable 1 — Idéation** de l'épreuve E5 (système d'information de Clean Elephant : Active Directory, GLPI, Checkmk et serveur web), dont ce site vitrine est le composant "serveur web".
