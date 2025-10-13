# AGENTS.md

This file provides structured guidance for AI coding assistants and agents working with the **Multisite Language Switcher** WordPress plugin.

## Project Overview
Multisite Language Switcher (MSLS) is a WordPress plugin that provides multilingual support by leveraging WordPress Multisite. It connects content across sites in a network to enable language switching and translation management. MSLS facilitates linking posts, pages, categories, and tags between sites representing different languages.

## Code Layout
- `MultisiteLanguageSwitcher.php` - Main plugin bootstrap and entry file
- `bin/` - Command-line scripts and utilities for development and maintenance
- `includes/` - Core PHP classes and services organized by functionality
- `assets/css/` - Stylesheets for admin or frontend usage (compiled from LESS when applicable)
- `assets/js/` - JavaScript used in admin UI and front-end integrations
- `assets/images/` - Icons and images used in admin UI
- `assets/css-flags/` - SVG flag icon set and related CSS
- `assets/flags/` - Legacy PNG flags (kept for backwards compatibility)
- `languages/` - Translation template (.pot) and localization files
- `tests/` - Unit and integration tests for core classes and features
- `vendor/` - Composer dependencies (do not edit)
- `composer.json` - PHP dependencies, autoload configuration, and project metadata (do not edit /vendor/ directly)
- `package.json` - JavaScript build/test tooling (e.g., Playwright, bundling) and npm scripts for lint/test/build

## Conventions
- PHP code follows **WordPress Coding Standards** (PHPCS configured accordingly).
- Namespaces use the prefix `lloc\Msls\` for all plugin classes.
- Strict typing is enabled (`declare(strict_types=1)`) in all PHP files.
- Core logic and services are organized within the `includes/` directory.
- News blocks and JavaScript components are in the `src/` directory.
- Supporting assets such as flag icons are stored in sub-directories of the folder `assets/`.
- Tests are organized under `tests/` with PHPUnit for PHP and Playwright for end-to-end JavaScript tests.

## Adding a New Feature
1. Add new service classes or modules inside the `includes/` directory, following existing namespace and class structure.
2. Register any new hooks or filters within the MslsPlugin's `init` method and ensure the classes are properly instantiated.
3. Add corresponding unit or integration tests in the `tests/` directory to cover new functionality.
4. If UI changes require new assets, add them to appropriate folders under `assets/`.
5. Follow the plugin’s coding standards and use strict typing throughout new code.

## Tests and CI
- PHPStan is configured and runs at a strict level to enforce static analysis.
- PHPCS checks PHP code against WordPress Coding Standards.
- PHPUnit is used for PHP unit and integration tests.
- Playwright is used for end-to-end testing of JavaScript and UI functionality.
- GitHub Actions workflows run CI on pull requests to validate code quality and test coverage.

## Things Agents Should Not Do
- Do not edit files under `/vendor/` as they are managed by Composer dependencies.
- Do not modify build artifacts or compiled files in any `/build/` directories if present.
- Do not change language translation files directly outside of the official localization workflow.
- Do not alter the plugin header in `MultisiteLanguageSwitcher.php`.

## References
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Multisite Documentation](https://wordpress.org/documentation/article/create-a-network/)
- [Multisite Language Switcher Support](https://wordpress.org/support/plugin/multisite-language-switcher/)

---

## Machine-Readable Summary

```yaml
project: Multisite Language Switcher
type: wordpress-plugin
layout:
  root:
    - MultisiteLanguageSwitcher.php
    - composer.json
    - package.json
    - README.md
    - CHANGELOG.md
    - languages/
    - vendor/
  includes/: core PHP classes and services
  bin/: command-line utilities and scripts
  tests/: unit and integration tests
  assets/: css, flag, and icon assets
  js_dir: src/
files:
  - README.md
  - CHANGELOG.md
  - composer.json
  - package.json
  - MultisiteLanguageSwitcher.php
ci:
  phpstan: enabled, strict level
  phpcs: wordpress-coding-standards
  phpunit: required
  playwright: end-to-end testing
  github_actions: runs on PRs
rules:
  - no edits in vendor/
  - no edits in build/ directories
  - language files only via localization workflow
  - do not change plugin header in MultisiteLanguageSwitcher.php
architecture:
  bootstrap: MultisiteLanguageSwitcher.php
  core: includes/
  cli_tools: bin/
assets:
  css-flags/: flag icons and styles
  css/: stylesheets
  js/: transpiled/build JavaScript files
  images/: icons used in admin UI
  flags/: legacy PNG flag assets
interfaces:
  - ServiceInterface: for core services
  - HookableInterface: for classes registering hooks
i18n:
  text_domain: msls
  template: languages/msls.pot
admin:
  menu_slug: msls
  capability: manage_network
```
