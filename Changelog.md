## 3.0.0

* Add Quick Create for translations: create the translated post straight from the editor metabox, or pick a source post on the new "Add from Translation" submenu (single and bulk), backed by a REST endpoint and switchable in the settings.
* Add `msls_quick_create_capability` so integrations can override the Quick Create permission checks, plus filters for the post data, the inserted post, the response, the untranslated-posts list, and the mapped taxonomy terms.
* Add filter hooks for the AJAX suggest results of the post and term metaboxes.
* Restructure `lloc\Msls\` into per-concern sub-namespaces (`Admin\`, `Blog\`, `ContentImport\`, `ContentTypes\`, `Frontend\`, `Link\`, `Options\`, `Registry\`, `RestApi\`). Every former flat `Msls*` class name keeps working through `lloc\Msls\Compat\Aliases`, registered from `includes/aliases.php`.
* Move the public helper functions into `includes/api.php` and make the `$attr` argument of `msls_get_switcher()` optional.
* Add a PHP-DI container for service construction, built on first use by `lloc\Msls\Container::get()`.
* Documentation: add a developer reference under `docs/` (public API, hooks, snippets, acknowledgements) and refresh the class and package diagrams.
* Fix: load the backwards-compatibility aliases and the `msls_*()` functions when the plugin file is included instead of on `plugins_loaded`, and keep the settings page slug handed to `msls_admin_register` at its pre-3.0 value. Add-ons such as MslsMenu load before the plugin and check `class_exists( 'lloc\Msls\MslsOptions' )` before registering anything, which silently disabled them — no add-on settings section, and no switcher in the nav menu.
* Fix: check authorization on the destination post during content import, and correct the ContentImporter permission and post type checks.
* Fix: do not fall back to `home_url()` for taxonomy and query archives.
* Fix: broken links on the page for the latest posts.
* Fix: several issues in the blog collection.
* i18n: close gaps in the WP-CLI messages and the Quick Create button title.
* Internal: strict typing throughout, PHPStan level 8 clean, `ABSPATH` guards, Plugin Check and PHPCS findings addressed, wp-env setup for local multisite development.
* Maintenance: numerous dependency updates.

## 2.10.1
* Fix: Deprecated function warning pointed to non-existent function.
* Documentation: update README.md code snippets to reflect new function names.

## 2.10.0

* Add prefixed public helper functions (msls_get_*, msls_the_msls) with deprecation shims for legacy names to satisfy WPCS while staying backward compatible.
* Expose action hook names as constants for safer programmatic use.
* Accessibility: add aria-current="page" on the active language link.
* Fix: resolve path-related issue affecting asset/loader resolution.
* Internal: reorganized folders for clearer structure; include composer.json in release artifacts.
* Maintenance: numerous dependency updates (WordPress build tooling, Playwright/e2e utils, Node types, security bumps such as tar-fs, dotenv, js-yaml, express).

## 2.9.6
* Alternate links are now printed without the title attribute.
* Fix in ImportCoordinates

## 2.9.5
* Importer base and ImportCoordinates tested and refactored by @lloc in https://github.com/lloc/Multisite-Language-Switcher/pull/402
* Components fixed

## 2.9.4
* Fix: type casting for msls_id in render_option call

## 2.9.3
* Bugfix Welsh css flag

## 2.9.2
* Addressed some of the errors that were reported by PHPStan
* Upgrade of PHPUnit to version 10
* Raise coverage
* Plugin check workflow added and existing workflows updated
* Updated JS dependencies
* Fix for build script
* Security fixes
* Fix double output

## 2.9.1
* Suggest Field in Post Editor Metabox: This feature allows you to input either numeric or alphanumeric values. If you enter a number, it’s treated as a Post ID. If you enter text, the field will suggest posts with matching titles.

## 2.9.0
* Gato GraphQL integration with 3 new functions: msls_get_post, msls_get_tax and msls_get_query
* Bugfix - missing action call 

## 2.8
* Bugfix: Content filter
* Bugfix: Category link
* Bugfix: Filter _GET request
* lots of bugfixes, testing & refactoring
* Plugin Check issues addressed

## 2.7
* flags/labels in the adminbar works now for every user-role
* Block reorganized
* WordPress' compatibility changed to min 6.1
* PHP code-beautifier added

## 2.6
* WordPress 6.5 tested
* WooCommerce product categories regression fixed by @nowori
* Links to translate.wordpress.org added by @patriciabt
* lots of code improvements in tests and codebase
* Style loading in admin_bar reviewed
* Prevention of double output in taxonomy edit-screens
* CSS fix for fields in meta-boxes
* Fixes in code and documentation
* New API function `msls_blog( string $locale ): ?MslsBlog;`
* New API function `msls_blog_collection(): MslsBlogCollection;`
* Text labels of languages in the backend
* Set PHP 7.4 as minimum requirement
* Set WordPress 5.6 as minimum requirement
* Blavatar fix 

## 2.5
* CodeSniffer installed for compatibility check with PHP 8.1
* CSS/SVG Flags refreshed
* WordPress 6.3 tested
* Compatibility issue with PHP 8.1 regarding FILTER_SANITIZE_STRING
* Compatibility issue with E_NOTICE and PHPUnit 10
* Avoid notice on Appearance > Widgets admin page
* Language files reviewed
* Legacy tests removed
* Fix #192 MslsCustomColumn/MslsAdminIcon fatal error (props @nelgmo)
* Fix #184 - use type of post and not only request (props @arnowelzel)
* Adding filters for customizing the "hreflang" output in the page header (props @jacksoggetto)
* Improvements in code quality
* Secondary buttons from MetaBoxes removed
* Flags refreshed
* Tested with 5.9
* Bugfix for request

[...]

## 0.1
* First version
