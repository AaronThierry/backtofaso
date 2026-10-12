# Déploiement de Back to Burkina sur le VPS Hostinger

Le VPS fait déjà tourner **travel_express** (Nginx, PHP 8.2-FPM, MySQL, Certbot).
On ajoute Back to Burkina **à côté**, sans toucher à la configuration existante.

| Élément | Valeur |
|---|---|
| Chemin | `/var/www/backtoburkina` |
| Base | `backtoburkina` / utilisateur `btb_user` |
| Domaine | `backtoburkina.bf` (+ `www`) — à adapter |
| PHP | 8.2 (Laravel 12 demande ≥ 8.2) |

---

## 0. Avant de commencer (sur le PC)

1. Tester en local : `php artisan migrate --seed`, `php artisan test`, parcourir les écrans.
2. Créer un dépôt GitHub privé `backtoburkina`, puis :

```bash
cd "C:\PROJET LARAVEL\backtoburkina"
git init
git add -A
git commit -m "Back to Burkina : première version"
git branch -M main
git remote add origin https://github.com/AaronThierry/backtoburkina.git
git push -u origin main
```

3. DNS : chez le registraire du domaine, créer deux enregistrements **A** vers l'IP du VPS :
   `backtoburkina.bf` et `www.backtoburkina.bf`.
   Vérifier avec `nslookup backtoburkina.bf` (la propagation peut prendre quelques heures).

---

## 1. Vérifier le serveur

```bash
ssh root@srv1176393.hstgr.cloud
php -v                      # doit afficher 8.2 ou plus
composer self-update
node -v                     # 20 ou plus
sudo apt install -y php8.2-intl php8.2-gd php8.2-zip php8.2-bcmath php8.2-mbstring php8.2-xml php8.2-curl php8.2-mysql
sudo systemctl restart php8.2-fpm
```

## 2. Base de données

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE backtoburkina CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'btb_user'@'localhost' IDENTIFIED BY 'MOT_DE_PASSE_FORT';
GRANT ALL PRIVILEGES ON backtoburkina.* TO 'btb_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 3. Récupérer le projet

```bash
cd /var/www
sudo git clone https://github.com/AaronThierry/backtoburkina.git backtoburkina
cd backtoburkina
sudo chown -R www-data:www-data /var/www/backtoburkina
sudo chmod -R 775 storage bootstrap/cache

sudo -u www-data composer install --optimize-autoloader --no-dev
npm ci && npm run build
```

## 4. Fichier .env de production

```bash
sudo cp .env.example .env && sudo nano .env
```

Valeurs à changer :

```env
APP_NAME="Back to Burkina"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://backtoburkina.bf

LOG_LEVEL=error

DB_DATABASE=backtoburkina
DB_USERNAME=btb_user
DB_PASSWORD=MOT_DE_PASSE_FORT

QUEUE_CONNECTION=sync
SESSION_SECURE_COOKIE=true

# Indispensable : un code est envoyé par e-mail à l'inscription et à chaque connexion
MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@backtoburkina.bf
MAIL_PASSWORD=MOT_DE_PASSE_DE_LA_BOITE
MAIL_FROM_ADDRESS="noreply@backtoburkina.bf"

OTP_SHOW_IN_DEV=false
```

```bash
sudo chown www-data:www-data .env
sudo -u www-data php artisan key:generate --force
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan storage:link
```

**Ne pas lancer `db:seed` en production** (ce sont des données de démonstration). Créer seulement le compte administrateur, avec une **adresse e-mail réelle que vous consultez** : chaque connexion demande un code envoyé à cette adresse.

```bash
sudo -u www-data php artisan tinker
```

```php
App\Models\User::create(['first_name'=>'Admin','last_name'=>'Travel Express','phone'=>'+226XXXXXXXX','email'=>'VOTRE_ADRESSE@exemple.com','password'=>'MOT_DE_PASSE','role'=>'admin','email_verified_at'=>now(),'onboarded_at'=>now()]);
App\Models\Conversation::superGroup()->users()->syncWithoutDetaching([App\Models\User::first()->id => ['role'=>'admin']]);
exit
```

Puis :

```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

## 5. Nginx

```bash
sudo nano /etc/nginx/sites-available/backtoburkina
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name backtoburkina.bf www.backtoburkina.bf;
    root /var/www/backtoburkina/public;

    client_max_body_size 20M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Le service worker ne doit jamais être mis en cache, sinon les mises à jour n'arrivent pas
    location = /sw.js {
        add_header Cache-Control "no-cache, no-store, must-revalidate";
        try_files $uri =404;
    }
    location = /manifest.webmanifest {
        default_type application/manifest+json;
        try_files $uri =404;
    }
    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 120;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/backtoburkina /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

> Ne pas supprimer la configuration de travel_express.

## 6. HTTPS (obligatoire pour la PWA et l'application mobile)

```bash
sudo certbot --nginx -d backtoburkina.bf -d www.backtoburkina.bf
sudo certbot renew --dry-run
```

Les ports 80 / 443 sont déjà ouverts pour travel_express : rien à changer dans `ufw`.

## 7. Script de mise à jour

```bash
sudo nano /var/www/backtoburkina/deploy.sh
```

```bash
#!/bin/bash
set -e
cd /var/www/backtoburkina
php artisan down || true
git pull origin main
sudo -u www-data composer install --optimize-autoloader --no-dev
npm ci && npm run build
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo chown -R www-data:www-data storage bootstrap/cache
php artisan up
echo "Déploiement terminé"
```

```bash
sudo chmod +x /var/www/backtoburkina/deploy.sh
```

Ensuite, à chaque modification : `git push` sur le PC, puis `./deploy.sh` sur le VPS.

## 8. Sauvegarde quotidienne de la base

```bash
sudo mkdir -p /var/backups/backtoburkina
sudo crontab -e
```

```cron
30 2 * * * mysqldump -u btb_user -p'MOT_DE_PASSE_FORT' backtoburkina | gzip > /var/backups/backtoburkina/db_$(date +\%F).sql.gz && find /var/backups/backtoburkina -mtime +14 -delete
```

Sauvegarder aussi `storage/app` (documents des dossiers, avatars, fichiers du chat).

## 9. Vérifications après mise en ligne

- `https://backtoburkina.bf` s'affiche, cadenas HTTPS valide
- Créer un compte de test → le code arrive par e-mail (regarder aussi les spams), puis se reconnecter → un nouveau code arrive
- Test d'envoi direct : `sudo -u www-data php artisan tinker --execute="Mail::raw('Test', fn (\$m) => \$m->to('vous@exemple.com')->subject('Test'));"`
- Envoyer un document dans le dossier, un message et un fichier dans le chat
- Sur Android (Chrome) : menu ⋮ › « Installer l'application »
- Logs : `tail -f storage/logs/laravel.log` et `sudo tail -f /var/log/nginx/error.log`

## 10. Application mobile

Dans `capacitor.config.json`, `server.url` doit valoir `https://backtoburkina.bf/accueil`, puis sur le PC :
`npx cap add android`, `npx cap sync`, `npx cap open android`.
