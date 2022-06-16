## Requirements:

- PHP 8+
- Composer
- [Symfony Cli](https://symfony.com/download) - for running local web-server
- Docker - for running local web-server
- Docker-compose - for running local web-server

---

## Setup

### Automatic

_This will also load data fixtures!_

1. Get setup script:
   `wget https://raw.githubusercontent.com/AnonymusBadger/mediaflex_recruitment_task/master/setup.sh`
2. Make executable:
   `chmod +x setup.sh`
3. Run:
   `./setup.sh`
4. Cd to project dir:
   `cd mediaflex_recruitment_task/`
5. Follow step 6 from **Manual Install**

One line: `wget https://raw.githubusercontent.com/AnonymusBadger/mediaflex_recruitment_task/master/setup.sh && chmod +x setup.sh && ./setup.sh && cd mediaflex_recruitment_task/`

:warning: If you get **`MySQL server has gone away`** error follow step 5 from **Manual Install** then **load data fixtures**.

### Manual

1. Clone:  
   `git clone https://github.com/AnonymusBadger/mediaflex_recruitment_task.git && cd mediaflex_recruitment_task/`
2. Run docker-compose:  
   `docker-compose up -d`
3. Install dependencies:  
   `symfony composer install`
4. Generate JWT keys:
   `php bin/console lexik:jwt:generate-keypair`
5. Execute database migrations:  
   `symfony console doct:mig:mig --no-interaction`
6. Start server:
   - a) With TLS: `symfony server:start -d` (requires `symfony server:ca:install` to run if not certificate exists)
   - b) Without TLS: `symfony server:start -d --no-tls`
7. To browse api documentation go to: `localhost:8000/api/docs`

---

### Browser exposed urls:

- `localhost:8000/api/docs` - browse api documentations with the power of SwaggerUI!
- `localhost:8080` - PhpMyAdmin

---

## Testing

#### Data fixtures

1. Data fixtures provide 3 predefined users:

   - `admin@user.com`
   - `moderator@user.com`
   - `user@user.com`
   - Password for each: `pass`

2. And 5 predefined applications:
   - `AppWithAdmin`
   - `AppWithModerator`
   - `AppWithUser`
   - `AppWithMany` (User, Moderator)
   - `AppWithoutAny`

### Manual

1. Load data fixtures:
   `symfony console doct:fix:load -n` :warning: **!!! THIS WILL PURGE YOUR DATABASE !!!**
2. Open browser Api Documentation (`localhost:8000/api/docs`)
3. Get token:
   1. Execute `/api/login` request with specified user credentials in request body
   2. Copy token value from response
   3. Authorize (green, top-right button) by inputing `Bearer {token}` in value field
4. Or use anonymously

### Automated

1. Make sure you have `sqlite` support enabled in your PHP setup
2. Run all tests with `php bin/phpunit -c phpunit.xml.dist`
