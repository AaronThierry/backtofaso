# Back to Burkina — application web, PWA et mobile (Capacitor)

Plateforme Travel Express : candidature pour étudier en Chine, suivi du dossier, communauté (chat, super groupe, groupes) et vivier de talents pour le retour à l’emploi.

- **Laravel 12** (PHP 8.2 ou plus), Blade, **Tailwind CSS 4**, **Alpine.js**
- **MySQL / MariaDB**
- **PWA** (manifest + service worker) et **Capacitor 7** pour Android / iOS

---

## 1. Installation (Windows : Laragon, XAMPP ou WAMP)

Prérequis : PHP ≥ 8.2 (extensions `pdo_mysql`, `mbstring`, `fileinfo`, `gd`), Composer, Node.js ≥ 20, MySQL.

```bash
cd "C:\PROJET LARAVEL\backtoburkina"

composer install
copy .env.example .env
php artisan key:generate
```

Créez la base `backtoburkina` dans MySQL (phpMyAdmin ou `CREATE DATABASE backtoburkina CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`), vérifiez `DB_USERNAME` / `DB_PASSWORD` dans `.env`, puis :

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build        # ou « npm run dev » pendant le développement
php artisan serve
```

Ouvrez http://localhost:8000

### Comptes de démonstration (mot de passe : `password`)

| Rôle | E-mail |
|---|---|
| Administrateur | admin@backtoburkina.bf |
| Conseiller Travel Express | conseil@backtoburkina.bf |
| Candidate (dossier en cours) | awa@exemple.bf |
| Étudiant en Chine | issouf@exemple.bf |
| Diplômés (vivier de talents) | talent1@exemple.bf à talent4@exemple.bf |
| Recruteur | recruteur@exemple.bf |

### Codes de vérification par e-mail

Un code est envoyé par e-mail à l’inscription (pour vérifier l’adresse) et à chaque connexion, après le mot de passe.

En local, avec `MAIL_MAILER=log`, aucun e-mail n’est envoyé : il est écrit dans `storage/logs/laravel.log`.
Avec `OTP_SHOW_IN_DEV=true` et `APP_ENV=local`, le code s’affiche aussi à l’écran.

En production, configurez un vrai serveur SMTP (`MAIL_*`), sinon personne ne pourra s’inscrire ni se connecter.

---

## 2. Ce que contient le projet

| Fonction | Routes | Fichiers principaux |
|---|---|---|
| Site public | `/` | `PageController`, `views/site/home` |
| Choix du profil | `/bienvenue` | `views/auth/welcome` |
| Inscription en 4 étapes (compte, code par e-mail, profil, notifications) | `/inscription…` | `Auth/RegisterController`, `Auth/OnboardingController`, `Services/OtpService` |
| Connexion (e-mail + mot de passe, puis code par e-mail) | `/connexion` | `Auth/LoginController` |
| Accueil | `/accueil` | `DashboardController` |
| Suivi de dossier + envoi de documents | `/dossier` | `DossierController`, modèles `Dossier`, `DossierDocument` |
| Vivier de talents (recherche, filtres, profil) | `/talents` | `TalentController`, modèle `TalentProfile` |
| Chat : super groupe (Annonces, Entraide, Emplois, Bourses), groupes publics/privés, messages privés, fichiers | `/chat`, `/groupes/nouveau` | `ChatController`, `GroupController`, modèles `Conversation`, `Message` |
| Compte | `/compte` | `AccountController` |

Règles métier :
- chaque nouvel inscrit rejoint automatiquement le **super groupe** ;
- dans le super groupe, **Annonces** et **Bourses** sont réservés à l’équipe (rôles `agent` et `admin`) ;
- un futur étudiant ou un étudiant en Chine reçoit un **dossier** avec 5 documents à fournir ;
- un diplômé reçoit un **profil talent**, visible des recruteurs seulement après vérification (`verified_at`) ;
- les documents du dossier sont stockés en privé (`storage/app/private`), pas dans `public`.

Le chat se rafraîchit toutes les 5 secondes. Pour du temps réel, branchez **Laravel Reverb** (`php artisan install:broadcasting`).

La charte (bleu iOS `#0071E3`, doré `#8E6202`, gris ardoise) est définie dans `resources/css/app.css` (`@theme`).

---

## 3. PWA

Déjà en place : `public/manifest.webmanifest`, `public/sw.js` (page hors ligne `/hors-ligne`), icônes dans `public/icons`.
En HTTPS, Chrome / Edge proposent « Installer l’application » ; sur iPhone : Safari › Partager › « Sur l’écran d’accueil ».

---

## 4. Application mobile avec Capacitor

L’application native charge le site en ligne (rendu serveur Laravel) : une seule base de code pour le web, la PWA, Android et iOS.

1. Déployez le site en HTTPS (ex. `https://backtoburkina.bf`) et mettez la bonne adresse dans `capacitor.config.json` → `server.url`.
2. Ajoutez les plateformes :

```bash
npm install
npx cap add android
npx cap add ios          # sur un Mac uniquement
npx cap sync
npm run cap:android      # ouvre Android Studio → Run / Build APK
```

Icônes et écran de démarrage : `npx @capacitor/assets generate` à partir de `public/icons/icon-1024.png`.

Pour tester sur un téléphone sans déployer : lancez `php artisan serve --host=0.0.0.0`, puis mettez temporairement `"url": "http://IP-DE-VOTRE-PC:8000/accueil"` et `"cleartext": true` dans `capacitor.config.json`.

---

## 5. Tests

```bash
php artisan test
```

---

## 6. À compléter avant la mise en production

- Serveur SMTP de production (indispensable : inscription et connexion passent par un code envoyé par e-mail)
- Back-office conseiller (valider les documents, faire avancer les étapes, vérifier les profils talents)
- Notifications push (Firebase Cloud Messaging via `@capacitor/push-notifications`)
- Textes marqués `[…]` (frais, téléphone, universités partenaires, photo du fondateur)
