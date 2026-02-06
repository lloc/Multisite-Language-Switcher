# AGENTS.md

WordPress plugin providing multilingual support via Multisite - connects content across sites for language switching.

## Structure
- `MultisiteLanguageSwitcher.php` - Plugin bootstrap (do not modify header)
- `includes/` - Core PHP classes (`lloc\Msls\` namespace)
- `src/` - JavaScript source components
- `assets/` - CSS, JS, flags, images
- `tests/` - PHPUnit + Playwright tests
- `vendor/` - Composer dependencies (do not edit)

## Conventions
- WordPress Coding Standards (PHPCS)
- Strict typing (`declare(strict_types=1)`)
- Text domain: `msls`

## CI
PHPStan (strict) | PHPCS | PHPUnit | Playwright | GitHub Actions on PRs

## Do Not
- Edit `vendor/` or `build/` directories
- Modify language files outside localization workflow
- Change plugin header in `MultisiteLanguageSwitcher.php`