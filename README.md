# AstroBook API

Forged in the legacy of Sirius, AstroBook guides the Dogstronauts in orchestrating their missions and managing galactic resources — a powerful system that centralizes event coordination and resource management across the galaxy.

[![PHP](https://img.shields.io/badge/PHP-8.4-777bb3?logo=php&logoColor=white)](https://www.php.net/releases/8.4/en.php)
[![Symfony](https://img.shields.io/badge/Symfony-7.3-000?logo=symfony)](https://symfony.com/)
[![API Platform](https://img.shields.io/badge/API%20Platform-4.2-4eb7bc)](https://api-platform.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENCE)

[![Tests](https://github.com/dogstronauts/astrobook/actions/workflows/tests.yaml/badge.svg?branch=1.0)](https://github.com/dogstronauts/astrobook/actions/workflows/tests.yaml)
[![Code Quality](https://github.com/dogstronauts/astrobook/actions/workflows/code-quality.yaml/badge.svg?branch=1.0)](https://github.com/dogstronauts/astrobook/actions/workflows/code-quality.yaml)
[![Validation](https://github.com/dogstronauts/astrobook/actions/workflows/validation.yaml/badge.svg?branch=1.0)](https://github.com/dogstronauts/astrobook/actions/workflows/validation.yaml)
[![Security](https://github.com/dogstronauts/astrobook/actions/workflows/security.yaml/badge.svg?branch=1.0)](https://github.com/dogstronauts/astrobook/actions/workflows/security.yaml)

Homepage: https://astrobook.dogstronauts.com

## Table of Contents
- Overview
- Quick Start
- Configuration
- API & Documentation
- Testing
- Development Workflow & Quality
- Contributing
- License
- Support

## Overview
AstroBook centralizes event coordination and resource management across the galaxy. It is built with Symfony and API Platform, follows Domain-Driven Design (DDD), and ships with first‑class tooling (OpenAPI export, CI, static analysis, fixtures, and functional tests).

## Quick Start

1) Clone & bootstrap
```bash
git clone https://github.com/dogstronauts/astrobook
cd astrobook
cp compose.override.yaml.dist compose.override.yaml
cp .env .env.local
```

2) Configure your environment (.env.local)
- Uncomment and adjust variables as needed.

3) Start the stack
```bash
docker compose up -d --wait
```

You should now see the API docs at: http://localhost:8080

### Tips
- Install dependencies & generate JWT keys:
```bash
docker compose exec app-php composer install
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

## API & Documentation
- Swagger UI (API Platform): http://localhost:8080/docs
- Export OpenAPI JSON (root/openapi.json):
```bash
docker compose exec app-php composer run run:export-openapi-doc
```

## Testing
Run tests:
```bash
docker compose exec app-php composer run run:tests
# or
docker compose exec app-php php bin/phpunit --testdox
```
## Development Workflow & Quality
```bash
docker compose exec app-php composer run run:php-cs-fixer
docker compose exec app-php composer run run:rector
```

## Contributing
Contributions are welcome! Please see:
- CONTRIBUTING.md
- CODE_OF_CONDUCT.md

## License
MIT - see LICENCE.

## Support
- Issues: https://github.com/dogstronauts/astrobook/issues
- Source: https://github.com/dogstronauts/astrobook
