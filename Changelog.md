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
