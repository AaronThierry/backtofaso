#!/bin/bash
# Script de deploiement VPS - Back to Burkina
# Usage: bash deploy.sh   (depuis la racine du projet, avec un .env deja configure)

set -euo pipefail
cd "$(dirname "$0")"

STEP="initialisation"
on_error() {
    echo ""
    echo "=========================================="
    echo "  ECHEC a l'etape : $STEP"
    echo "=========================================="
    echo "  Le site est encore en mode maintenance."
    echo "  Corrigez le probleme puis relancez le script,"
    echo "  ou remettez le site en ligne : php artisan up"
    exit 1
}
trap on_error ERR

echo "=========================================="
echo "  DEPLOIEMENT BACK TO BURKINA"
echo "=========================================="

if [ ! -f .env ]; then
    echo "Fichier .env introuvable : copiez .env.example vers .env et configurez-le avant de deployer."
    exit 1
fi

STEP="[1/9] Mode maintenance"
echo ""; echo "$STEP..."
php artisan down --retry=60 || true

STEP="[2/9] Git pull"
echo ""; echo "$STEP..."
git pull --ff-only origin main

STEP="[3/9] Dependances PHP"
echo ""; echo "$STEP..."
composer install --no-dev --optimize-autoloader --no-interaction

STEP="[4/9] Build des assets (Vite)"
echo ""; echo "$STEP..."
npm ci
npm run build

STEP="[5/9] Verification APP_KEY"
echo ""; echo "$STEP..."
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "  -> Generation de la cle APP_KEY..."
    php artisan key:generate --force
fi

STEP="[6/9] Migrations base de donnees"
echo ""; echo "$STEP..."
php artisan migrate --force
# Pas de db:seed ici : DatabaseSeeder cree des comptes de demonstration
# (mot de passe "password"), a ne jamais executer en production.

STEP="[7/9] Lien storage et permissions"
echo ""; echo "$STEP..."
[ -L public/storage ] || php artisan storage:link
chmod -R ug+rwX storage bootstrap/cache
if ! chown -R www-data:www-data storage bootstrap/cache 2>/dev/null; then
    echo "  -> ATTENTION : chown www-data impossible (relancez avec sudo si le serveur web n'a pas les droits)."
fi

STEP="[8/9] Caches optimises"
echo ""; echo "$STEP..."
php artisan optimize:clear
php artisan optimize

STEP="[9/9] Redemarrage des workers et remise en ligne"
echo ""; echo "$STEP..."
php artisan queue:restart
php artisan up

echo ""
echo "=========================================="
echo "  DEPLOIEMENT TERMINE AVEC SUCCES"
echo "=========================================="
