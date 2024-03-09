#!/bin/bash

#set -e

# dockerhost in hosts
echo -e "${BLUE}Add dockerhost in hosts file..${NOCOLOR}"
DOCKERHOST=$(/sbin/ip route|awk '/default/ { print $3 }')
echo "${DOCKERHOST} dockerhost" >> /etc/hosts
sed -i "s,set_real_ip_from.*$,set_real_ip_from ${DOCKERHOST};,g" /etc/nginx/sites-available/default

## .env setup
echo -e "${BLUE}Laravel .env setup..${NOCOLOR}"
[[ -v APP_URL ]] && sed -i "s,APP_URL=.*$,APP_URL=${APP_URL},g" .env
[[ -v APP_KEY ]] && sed -i "s,APP_KEY=.*$,APP_KEY=${APP_KEY},g" .env
# mail
[[ -v MAIL_HOST ]] && sed -i "s,MAIL_HOST=.*$,MAIL_HOST=${MAIL_HOST},g" .env
[[ -v MAIL_USERNAME ]] && sed -i "s,MAIL_USERNAME=.*$,MAIL_USERNAME=${MAIL_USERNAME},g" .env
[[ -v MAIL_PASSWORD ]] && sed -i "s,MAIL_PASSWORD=.*$,MAIL_PASSWORD=${MAIL_PASSWORD},g" .env
[[ -v MAIL_ENCRYPTION ]] && sed -i "s,MAIL_ENCRYPTION=.*$,MAIL_ENCRYPTION=${MAIL_ENCRYPTION},g" .env
[[ -v MAIL_FROM_ADDRESS ]] && sed -i "s,MAIL_FROM_ADDRESS=.*$,MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS},g" .env
[[ -v MAIL_FROM_NAME ]] && sed -i "s,MAIL_FROM_NAME=.*$,MAIL_FROM_NAME=${MAIL_FROM_NAME},g" .env
[[ -v MAIL_PORT ]] && sed -i "s,MAIL_PORT=.*$,MAIL_PORT=${MAIL_PORT},g" .env
# database
[[ -v DB_HOST ]] && sed -i "s,DB_HOST=.*$,DB_HOST=${DB_HOST},g" .env
[[ -v DB_DATABASE ]] && sed -i "s,DB_DATABASE=.*$,DB_DATABASE=${DB_DATABASE},g" .env
[[ -v DB_USERNAME ]] && sed -i "s,DB_USERNAME=.*$,DB_USERNAME=${DB_USERNAME},g" .env
[[ -v DB_PASSWORD ]] && sed -i "s,DB_PASSWORD=.*$,DB_PASSWORD=${DB_PASSWORD},g" .env
[[ -v DB_PORT ]] && sed -i "s,DB_PORT=.*$,DB_PORT=${DB_PORT},g" .env

[[ -v STRIPE_KEY ]] && sed -i "s,STRIPE_KEY=.*$,STRIPE_KEY=${STRIPE_KEY},g" .env
[[ -v STRIPE_SECRET ]] && sed -i "s,STRIPE_SECRET=.*$,STRIPE_SECRET=${STRIPE_SECRET},g" .env
[[ -v PRODUCT_SUBSCRIPTION_ID ]] && sed -i "s,PRODUCT_SUBSCRIPTION_ID=.*$,PRODUCT_SUBSCRIPTION_ID=${PRODUCT_SUBSCRIPTION_ID},g" .env
[[ -v STRIPE_WEBHOOK_SECRET ]] && sed -i "s,STRIPE_WEBHOOK_SECRET=.*$,STRIPE_WEBHOOK_SECRET=${STRIPE_WEBHOOK_SECRET},g" .env

service php8.1-fpm start

cd /var/www/html
echo -e "\nInstalling composer dependencies...\n"
gosu www-data composer install

echo -e "Installing node dependencies"
gosu www-data npm install

echo -e "Building Admin Panel app... \n"
gosu www-data npm run build

echo -e "Running migrations... \n"
gosu www-data php artisan migrate --force

echo "Running app.."
exec "$@"
