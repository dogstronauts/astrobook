# AstroBook API

Forged in the legacy of Sirius, AstroBook guides the Dogstronauts in orchestrating their missions and managing galactic resources — a powerful system that centralizes event coordination and resource management across the galaxy.

[![Tests](https://github.com/dogstronauts/astrobook/actions/workflows/tests.yaml/badge.svg?branch=main)](https://github.com/dogstronauts/astrobook/actions/workflows/tests.yaml)
[![Code Quality](https://github.com/dogstronauts/astrobook/actions/workflows/code-quality.yaml/badge.svg?branch=main)](https://github.com/dogstronauts/astrobook/actions/workflows/code-quality.yaml)
[![Validation](https://github.com/dogstronauts/astrobook/actions/workflows/validation.yaml/badge.svg?branch=main)](https://github.com/dogstronauts/astrobook/actions/workflows/validation.yaml)
[![Security](https://github.com/dogstronauts/astrobook/actions/workflows/security.yaml/badge.svg?branch=main)](https://github.com/dogstronauts/astrobook/actions/workflows/security.yaml)
[![PHP](https://img.shields.io/badge/PHP-8.4-777bb3?logo=php&logoColor=white)](https://www.php.net/releases/8.4/en.php)
[![Symfony](https://img.shields.io/badge/Symfony-7.3-000?logo=symfony)](https://symfony.com/)
[![API Platform](https://img.shields.io/badge/API%20Platform-4.1-3b5998)](https://api-platform.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENCE)

Homepage: https://astrobook.dogstronauts.com

## Table of Contents
- Overview
- Quick Start
- Configuration
- API & Documentation
- Testing
- Contributing
- Security
- License
- Support

## Overview
AstroBook is a powerful system that centralizes event coordination and resource management across the galaxy. It is built with Symfony and API Platform, following DDD principles and offering first‑class developer tooling (OpenAPI export, CI, static analysis, fixtures, and functional tests).


## Quick Start

1) Clone & bootstrap
```bash
git clone https://github.com/dogstronauts/astrobook
cd astrobook
cp compose.override.yaml.dist compose.override.yaml
cp .env .env.local
```

2) Configure your environment (.env.local)
- Open .env.local and uncomment the pre-filled variables.
- These are sane defaults provided to let you run the project locally out of the box.
- You can adjust them later as needed.

3) Start the stack
```bash
docker compose up -d --wait
```

You should now see the API docs at: http://localhost/docs

### Tips & next steps

- Install dependencies & generate JWT keys:
```bash
docker compose exec app-php composer install
# Generate the JWT keypair (idempotent)
docker compose exec app-php php bin/console lexik:jwt:generate-keypair --skip-if-exists
```

- Prepare the database:
```bash
docker compose exec app-php php bin/console doctrine:migrations:migrate -n
# (Dev) Load sample data
docker compose exec app-php composer run run:fixtures
```

- Generate a secure passphrase if needed:
```bash
openssl rand -base64 32
```

## Configuration
Key files:
- .env — defaults
- .env.local — your local overrides
- .env.test — test environment
- compose.override.yaml — local Docker overrides

## API & Documentation
- Swagger UI (API Platform): http://localhost/docs
- Export OpenAPI JSON (root/openapi.json):
```bash
docker compose exec app-php composer run run:export-openapi-doc
```



## Testing
Run all tests:
```bash
docker compose exec app-php composer run run:tests
# or
docker compose exec app-php php bin/phpunit --testdox
```
By type/group examples:
```bash
# Functional
docker compose exec app-php php bin/phpunit tests/Functional --testdox
# Integration
docker compose exec app-php php bin/phpunit tests/Integration --testdox
# Unit
docker compose exec app-php php bin/phpunit tests/Unit --testdox
# Group
docker compose exec app-php php bin/phpunit --group=endpoints
```


## Contributing
Contributions are welcome! Please see:
- CONTRIBUTING.md
- CODE_OF_CONDUCT.md

Local quality tools:
```bash
docker compose exec app-php composer run run:php-cs-fixer
docker compose exec app-php composer run run:rector
docker compose exec app-php vendor/bin/phpstan analyse
```

## Security
If you discover a security issue, please email: de25259ab1817bd62da981f2@dogstronauts.com. We will respond promptly.

## License
MIT — see LICENCE.

## Support
- Issues: https://github.com/dogstronauts/astrobook/issues
- Source: https://github.com/dogstronauts/astrobook
