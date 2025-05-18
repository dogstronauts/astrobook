# AstroBook API

REST API for the AstroBook platform, enabling event management and booking

## Architecture Documentation

Detailed architecture documentation is available in the [ARCHITECTURE.md](docs/ARCHITECTURE.md) file. This documentation includes:

## Technical Stack

### Backend
- **PHP 8.4**: Latest stable version with modern features and performance improvements
- **Symfony 7.3**: Enterprise-grade PHP framework for robust API development
- **API Platform 4.1**: Modern REST and GraphQL API framework with built-in documentation
- **Doctrine ORM**: Powerful object-relational mapper for database interactions

### Infrastructure
- **Docker**: Containerization for consistent development and deployment environments
- **Nginx**: High-performance web server and reverse proxy
- **PHP-FPM**: FastCGI Process Manager for efficient PHP request handling
- **PostgreSQL 17**: Advanced open-source database with robust features for complex data management

### Development Tools
- **PHPUnit**: Comprehensive testing framework for quality assurance
- **PHPStan**: Static analysis tool for code quality
- **PHP CS Fixer**: Code style enforcement tool
- **Rector**: Instant PHP code upgrades and automated refactoring

## Prerequisites

- Docker and Docker Compose

## Installation

Clone the repository:
```bash
git clone https://github.com/dogstronauts/astrobook
```

Configure environment variables:
```bash
cp .env .env.local
# Edit .env.local with your configurations
```

Generate a JWT passphrase:
```bash
# Generate a secure passphrase for JWT signing
openssl rand -base64 32

# Copy the generated passphrase to .env.local and assign it to JWT_PASSPHRASE
# Example: JWT_PASSPHRASE=your_generated_passphrase
```

Build and start Docker containers:
```bash
cp compose.override.yaml.dist compose.override.yaml
docker compose build --no-cache
docker compose up -d --wait
```

## Configuration

The project uses several configuration files:
- `.env`: default configuration
- `.env.local`: local configuration to create
- `.env.test`: configuration for tests
- `.env.dev`: configuration for development

## Documentation

- [API Documentation](https://linktodocumentation)
- [Technical Documentation](https://linktodocumentation)

## Testing

The project uses PHPUnit for testing. To run the tests, you must first configure the test environment (APP_ENV=test).

```bash
docker compose exec app-php bin/phpunit
```

## Contributing

Contributions are welcome! Please check our contribution guide for more details.

## License

This project is proprietary licensed.

## Security

To report a security vulnerability, please contact us directly.

## Support

For any questions or assistance, please contact us via [SUPPORT_CHANNEL].
