## 2.6.3

- Style loading in admin_bar reviewed
- Prevention of double output in taxonomy edit-screens

## 2.6.2

* CSS fix for fields in meta-boxes

## 2.6.1

* Fixes in code and documentation
* New API function `msls_blog( string $locale ): ?MslsBlog;`
* New API function `msls_blog_collection(): MslsBlogCollection;`
* Text labels of languages in the backend
 
## 2.6.0

* WordPress 6.4 tested
* Set PHP 7.4 as minimum requirement
* Set WordPress 5.6 as minimum requirement
* Blavatar fix 

## 2.5.11

* CodeSniffer installed for compatibility check with PHP 8.1
* CSS/SVG Flags refreshed

## 2.5.10

* WordPress 6.3 tested
* Compatibility issue with PHP 8.1 regarding FILTER_SANITIZE_STRING

## 2.5.9

* Compatibility issue with E_NOTICE and PHPUnit 10

## 2.5.8

* Avoid notice on Appearance > Widgets admin page
* Language files reviewed
* Legacy tests removed

## 2.5.7

* Fix #192 MslsCustomColumn/MslsAdminIcon fatal error (props @nelgmo)

## 2.5.6

* Fix #184 - use type of post and not only request (props @arnowelzel)
* Adding filters for customizing the "hreflang" output in the page header (props @jacksoggetto) 

## 2.5.5

* WordPress 6.0 tested
* Improvements in code quality

## 2.5.4

* Secondary buttons from MetaBoxes removed
* Flags refreshed
* Tested with 5.9
* Bugfix for request

## 2.4.12

* Flags regenerated (CSS and PNG)

## 2.4.11

### Bug fixes

* Content importer
* Flags regenerated (CSS and PNG)

## 2.4.10

### Features

* Filters added to control output of alternate url and blog-permalink url of blog-specific hreflang tags

## 2.4.9

### Bug fix 

* Language codes of formal languages map now to the correct flag (de_DE_formal -> de, de_CH_formal -> ch, nl_NL_formal -> nl)

## 2.4.8

### Hotfix 

* hotfix call to get_super_admin() is to early

## 2.4.7

### Bug fixes

* docs updated for the possibility to remove action for alternative output in hreflang
* admin bar output is now exclusive for super admins

## 2.4.6

### Bug fix

* Notice in HrefLang object

## 2.4.5

### Hotfix 

* hotfix for **admin-bar** primary blog could be excluded
 
## 2.4.4

### Features

* the value for **hrefLang** in head is now more specific. It will now return a specific language-code if there are similar locales like 'en_US' and 'en_GB' ('en-US' and 'en-GB' in this case).
* Sites in the **admin-bar** show now the description-part.

## 2.4.3

### Bug Fixes

* fix for alternate links for hreflang

## 2.4.2

### Bug Fixes

* new **SVG** flags use now the same logic as the PNG flags for getting the right name  

## 2.4.1

### Bug Fixes

* use of \Locale class removed because it expects PHP Intl installed which seems not the standard case   

## 2.4.0

* Min PHP Version is now 7.0
* Code enhancements & Bugfixes
* https://github.com/lloc/Multisite-Language-Switcher/commits/master

## 2.3.0

### Features

* **New API functions**
    - `get_msls_flag_url( string $language ): string;`
    - `get_msls_blog_description( string $language ): bool|string;`
    - `get_msls_permalink( string $locale ): string;`

## 2.2.0

### Features

* **Flags** There was a general improvement how MSLS gets the icons
* **New filter**: 'msls_supported_post_types' is now available if you need MSLS for non-public post types

## 2.1.1

### Bug Fixes

* **Taxonomies** ternary stacking problem solved

## 2.1.0

### Features

* **Gutenberg block** you'll find now also a new block in the widget-category
* **New shortcode** there is a new shortcode [sc_msls_widget] available that renders the widget

## 2.0.3

### Bug Fixes
* **Language lookup** `us` was saved but code looked up for `en_US` 

## 2.0.2

### Bug Fixes

* **Widget** namespace from base_id removed
* **Admin Options** was 2 times called

## 2.0.1

### Bug Fixes

* **Shortcode** corrected
* **Alternate href links** reintegrated

## 2.0

### Features

* **Content import**
    - new experimental content import feature introduced
* **PHP improvements**
    - 5.6 is minimum

## 1.2 (2018-02-01)

### Bug Fixes

* **MetaBox:**
    - WordPress Coding Standards
* **OptionsPage:**
    - set American English
    - admin language removed
    - menu slug corrected
* **Tests:**
    - MslsPostTag* corrected
    - blog collection injected
    - bootstrap updated
    - getMock removed
    - .travis.yml updated
    - WP-CLI plugin-tests bug fix

### Features

* **Blog slug:** 
    - save the slug in the primary blog
* **Composer:**
    - psr 0 autoload added
    - license updated
    - psr 4 autoloader added
    - travis deploy added
* **Filter:** 
    - filter 'check_url' in get_postlink refactored
    - new filter msls_get_postlink introduced
    - refactoring get_postlink()
* **Namespace:**
    - namespace completly introduced
* **PHP:**
    - set version 5.6 to minimal requirement
* **PhpDox:**
    - PhpDox for code documentation added
* **Rewrites:**
    - panels added
* **Languages:**
    - languages files for Mexican Spansh (es_MX) by Fernando Mata added


## 1.1
* Fix and enhancements for translated slugs

## 1.0.8
* Bugfix for issue [American English doesn't work with WordPress 4.3](https://wordpress.org/support/topic/american-english-doesnt-work-with-wordpress-43?replies=12#post-7791218)

## 1.0.7
* text domain is now 'multisite-language-switcher', translation files moved to refelect this change

## 1.0.6
* language files for Bulgarian (bg_BG) by Vencislav Raev added
* admin options page heading is now h1 instead of h2

## 1.0.5
* Filter 'msls_meta_box_render_select_hierarchical' closes issue [64](https://github.com/lloc/Multisite-Language-Switcher/issues/64)
* Bugfix for issue [73](https://github.com/lloc/Multisite-Language-Switcher/issues/73)
* language files for Norwegian (nb_NO) by Ibrahim Qraiqe added
* Bugfix for issue [Multisite Language Switcher Options is empty](https://wordpress.org/support/topic/multisite-language-switcher-options-is-empty)

## 1.0.4
* language files for Arabic (ar) by Mohamed Elwan added

## 1.0.3
* Bugfix: alternate hreflang for the current blog was empty
* Filter: 'msls_options_get_flag_icon' introduced
* Shortcode [sc_msls] added

## 1.0.2
* Bugfix: term links were damaged when term_base option were used
* Bugfix: en_GB & en_US both displayed as 'English' in Settings > MLS >Blog & Admin language
* Filter 'msls_meta_box_render_input_button' introduced

## 1.0.1
* Bugfix: filter internal types from get_post_stati

## 1.0
* you can choose now your frontend/backend language from the installed languages in the plugins settings
* support for 25 languages
* numerous enhancements and fixes (coding style, sql cache, performance, api docs, unit tests ...)

[...]

## 0.9.9
* Option to transform the dropdowns for choosing the connections into text-inputs with jquery-ui autocomplete
* Plugin cleans up the option-tables now when uninstalled
* Problem with multiple admin resolved, there is now a "Reference User"-option available
* a lot of new filters/action, more on this later in the Wiki
* now you can use `<?php if ( function_exists( 'msls_filter_string' ) ) msls_filter_string(); ?>` if you'ld like to print the "hint"-string anywhere in your templates
* language files for ca_ES by Joan López, cs_CZ by Rastr and bn_BD by Md. Nazmul Huda added
* MslsCustomFilter-Class by Maciej Czerpiński
* and tons of other minor improvements

[...]

## 0.9.8
* Fix of the "MslsOptionsQueryDay.php"-bug
* language files for es_ES updated by [José Luis Pajares](http://gelo.tv/)

## 0.9.7
* further bugfixes and enhancements

## 0.9.6
* a lot of bugfixes and enhancements
* some modifications - how the meta box handles hierarchical post types - by [Joost de Keijzer](http://dekeijzer.org/)
* behaviour - how the plugin collects the blogs - changed
* language files for ro_RO added

## 0.9.5
* new: Support for author- and date-archives
* language files for hr_HR added
* some modifications of the image path handling by [Tobias Bäthge](http://tobias.baethge.com/)

[...]

## 0.1
* first version
