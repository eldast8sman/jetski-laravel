#!/bin/bash

# Get the Elastic Beanstalk environment name
ENV_NAME=$(aws elasticbeanstalk describe-environments --query "Environments[?Status=='Ready'].EnvironmentName" --output text)

# Define S3 paths for different environments
if [[ "$ENV_NAME" == "LagosJetski-env" ]]; then
    ENV_FILE="s3://projectjson/env_variables.json"
elif [[ "$ENV_NAME" == "Jetski-Production-env" ]]; then
    ENV_FILE="s3://projectjson/env_variables_prod.json"=
else
    echo "Unknown environment: $ENV_NAME"
    exit 1
fi

# Download env.json file from S3 bucket
aws s3 cp "$ENV_FILE" /tmp/env.json

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