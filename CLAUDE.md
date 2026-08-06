# CLAUDE.md

This file is the single source of truth for coding agents working in this repository (`AGENTS.md` points here).

## About

Multisite Language Switcher (MSLS) is a WordPress plugin that adds multilingual support to WordPress multisite installations. It connects content (posts, pages, custom post types, taxonomies) across sites in a multisite network for language switching.

## Commands

### Testing
```bash
composer phpunit                    # Run PHPUnit test suite
composer phpunit -- --filter=TestClassName  # Run a single test class
composer phpunit -- --filter=testMethodName # Run a single test method
composer phpunit:clover             # Run tests with code coverage (XML)
composer phpunit:html               # Run tests with code coverage (HTML)
```

### Static Analysis & Linting
```bash
composer phpstan                    # PHPStan at level 8
composer phpcs                      # PHP compatibility check (7.4+)
vendor/bin/phpcs                    # WordPress coding standards (uses .phpcs.xml.dist)
```

### Building
```bash
npm run build                       # Build JS (uglify + less + Gutenberg block)
npm run build-msls-block            # Build only the Gutenberg block
```

### E2E Tests
```bash
npx playwright test                 # Run Playwright tests (against msls.co)
npx playwright test --ui            # Run with UI
```

### Local Development Environment
```bash
npx wp-env start                    # Start WordPress multisite via wp-env (PHP 8.3)
npx wp-env stop
```

## Architecture

### Repository Layout
- `MultisiteLanguageSwitcher.php` — plugin bootstrap
- `includes/` — core PHP classes
- `src/` — JavaScript source components
- `assets/` — CSS, JS, flags, images
- `docs/` — developer reference (API, hooks, snippets)
- `tests/` — PHPUnit and Playwright tests

### Namespace & Autoloading
- PSR-4: `lloc\Msls\` maps to `includes/`, split into per-concern sub-namespaces: `Admin\`, `Blog\`, `Cli\`, `Component\`, `ContentImport\`, `ContentTypes\`, `Data\`, `Db\`, `Frontend\`, `Link\`, `Options\`, `Registry\`, `Request\`, `RestApi\`
- PSR-4 (dev): `lloc\MslsTests\` maps to `tests/phpunit/`
- Plugin bootstrap: `MultisiteLanguageSwitcher.php` — defines constants, then on `plugins_loaded` requires `includes/aliases.php`, `includes/deprecated.php`, and `includes/api.php`, builds the PHP-DI container from `config.php`, and calls `lloc\Msls\Plugin::init()` plus `lloc\Msls\Cli\Cli::init()`
- **Backwards-compatibility aliases**: `includes/aliases.php` registers the ~60 pre-3.0 flat class names (`MslsOptions`, `MslsLink`, `MslsPlugin`, …) as `class_alias()` entries for their namespaced replacements. Write new code against the namespaced names; the aliases exist only for third-party consumers

### Key Patterns
- **Registry/Singleton**: `Registry\Instance` is the base class providing the `::instance()` static accessor (backed by `Registry\Registry`); `Registry\GetSet` extends it to add overloaded property access
- **Factory methods**: `Options\Options::create()`, `Options\Tax\Tax::create()`, `Options\Query\Query::create()`, `ContentTypes\ContentTypes::create()` return context-aware instances based on WordPress conditional tags (is_category, is_tag, is_day, etc.)
- **Options hierarchy**: `Options\Options` (base, extends `GetSet`) → `Options\Post\Post` (post translations) / `Options\Tax\Tax` → `Options\Tax\Term` → `Options\Tax\Category` (taxonomy translations) / `Options\Query\Query` → `Author`, `Day`, `Month`, `Year`, `PostType` (archive pages)
- **Link rendering**: `Link\Link` base class with variants (`Link\TextOnly`, `Link\ImageOnly`, `Link\TextImage`) — selected by the display index 0–3 from `Link\Link::get_types()`, controlled by admin settings
- **Content Import**: `ContentImport/` subsystem handles duplicating content across sites with importers for post fields, meta, terms, attachments, and thumbnails
- **REST API / Quick Create**: `RestApi/` exposes the endpoints behind the editor metabox button and the "Add from Translation" submenu (`Admin\TranslationPicker\`)

### Global API Functions
`includes/api.php` exposes the template functions: `msls_the_switcher()`, `msls_get_switcher()`, `msls_get_permalink()`, `msls_get_flag_url()`, `msls_blog_collection()`, etc. Legacy names (`the_msls()`, `get_the_msls()`, …) live in `includes/deprecated.php` and forward to them with a `_deprecated_function()` notice.

### Developer Documentation
`docs/` holds the reference material: `api.md` (public API functions), `hooks.md` (every action and filter), `snippets.md` (integration recipes), `acknowledgements.md` (credits and translators). Keep these in sync when adding or renaming a hook or an API function.

### Test Framework
- PHPUnit 10 with Brain\Monkey for WordPress function mocking
- Patchwork for redefining PHP internals (`filter_input`, `filter_input_array`, `filter_has_var`)
- Base test class: `MslsUnitTestCase` — sets up Monkey, stubs common WP escaping/i18n functions
- Tests mirror the source structure with a `Test` prefix: `includes/Options/Tax/Term.php` → `tests/phpunit/Options/Tax/TestTerm.php`

## CI

GitHub Actions runs PHPStan, PHPCS, PHPUnit, and Playwright on every pull request.

## Conventions

- WordPress Coding Standards enforced via PHPCS (tabs, Yoda conditions, WordPress function spacing)
- All classes use `declare(strict_types=1)`
- Text domain: `multisite-language-switcher` everywhere — in the plugin header and in every `__()` / `esc_html__()` call. Do not use `msls` as a text domain; it is the name of the plugin's option row (`get_option( 'msls' )`)
- Do not modify the plugin header in `MultisiteLanguageSwitcher.php`
- Do not edit `vendor/`, `build/`, `node_modules/`, or language files directly
