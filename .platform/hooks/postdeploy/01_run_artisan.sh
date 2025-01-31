#!/bin/bash

# Download env.json file from S3 bucket
aws s3 cp s3://projectjson/env_variables.json /tmp/env.json

# Parse env.json and create .env file
cat /tmp/env.json | jq -r 'to_entries[] | "\(.key)=\(.value)"' > /var/app/current/.env

# Download psychinsightsapp.json file from S3 bucket
aws s3 cp s3://projectjson/jetski.json /tmp/jetski.json

# Create directory if it doesn't exist
mkdir -p storage/app/public/fcm

# Copy psychinsightsapp.json to storage/app/public/fcm folder
cp /tmp/jetski.json storage/app/public/fcm/jetski.json

# Navigate to the Laravel app directory
cd /var/app/current

#Make Storage writable
sudo chown -R webapp:webapp storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Run Laravel Artisan commands
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan storage:link
# php artisan db:seed --force