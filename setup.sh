#!/bin/bash
cd "$(dirname "$0")"

git clone https://github.com/AnonymusBadger/mediaflex_recruitment_task.git &&
    cd mediaflex_recruitment_task/ &&
    docker-compose up -d &&
    symfony composer install &&
    symfony console doct:mig:mig --no-interaction -q &&
    php bin/console lexik:jwt:generate-keypair -q &&
    symfony console doct:fix:load -n -q
