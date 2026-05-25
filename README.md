# Multisite Language Switcher

A simple, powerful, and user-friendly plugin that adds multilingual support to your [WordPress multisite](https://wordpress.org/documentation/article/create-a-network/) installation, whether using multiple subdomains or folders. Multisite Language Switcher allows you to effortlessly manage translations for posts, pages, custom post types, categories, tags, and custom taxonomies.

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/multisite-language-switcher.svg)](https://wordpress.org/plugins/multisite-language-switcher/)
[![License](https://img.shields.io/badge/license-GPL_v2%2B-blue.svg)](http://opensource.org/licenses/GPL-2.0)
[![WordPress Tested](https://img.shields.io/wordpress/v/multisite-language-switcher.svg)](https://wordpress.org/plugins/multisite-language-switcher/)
[![codecov](https://codecov.io/gh/lloc/Multisite-Language-Switcher/graph/badge.svg?token=IlD4bX4KZ4)](https://codecov.io/gh/lloc/Multisite-Language-Switcher)
[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/lloc/Multisite-Language-Switcher)

## Where to get the plugin

[Download](http://downloads.wordpress.org/plugin/multisite-language-switcher.zip) the latest stable from the [WordPress Plugin Directory](https://wordpress.org/plugins/multisite-language-switcher/), and please remember to give this plugin [a five-star rating](https://wordpress.org/support/plugin/multisite-language-switcher/reviews/#new-post).

_Please note that while the master branch on GitHub is intended to be stable, it is not recommended for production use. Instead, please use the [official releases](https://github.com/lloc/Multisite-Language-Switcher/releases) for deployment._

If you plan to use the GitHub repository on a server, run `composer run install-prod`. It installs the production PHP dependencies and builds the assets required at runtime (flag definitions, JavaScript bundles, CSS, and the Gutenberg block). The script requires PHP, Composer, and Node.js / npm to be available on the machine.

## Installation Instructions

*  Via WordPress Dashboard:
    *  Go to your WordPress dashboard.
    *  Navigate to `Network Admin` > `Plugins`.
    *  Click on `Add New Plugin`.
    *  Search for "Multisite Language Switcher".
    *  Click `Install Now` and then `Activate`.

* Manual Installation:
    *  Download the plugin and unzip it using your preferred program.
    *  Upload the entire plugin directory to your WordPress plugin directory (/wp-content/plugins).
    *  Navigate to `Network Admin` > `Plugins`.
    *  Click on `Activate` for the Multisite Language Switcher plugin.

*  Configuration:
    *  After activation, navigate to `Settings` > `Multisite Language Switcher` in each blog to configure the plugin.

## Features and Capabilities

*  Connect Translations for Pages and Posts:
    *  Navigate to `Posts` > `Edit` or `Pages` > `Edit` to link your translated content.

*  Connect Translations for Categories and Tags:
    *  Go to `Posts` > `Categories` or `Posts` > `Tags` to associate your translated categories and tags.

*  Connect Custom Post Types and Custom Taxonomies:
    *  Easily manage translations across different custom post types and taxonomies.

*  Utilize Widgets, Shortcodes, and Content Filters:
    *  Use the widget or the Gutenberg block.
    *  Use the `[sc_msls]` shortcode to render the language switcher, or `[sc_msls_widget]` for the widget variant.
    *  Set the content filter to display a hint to users when a translation is available.

*  Optional Theme Integration:
    *  Insert the following PHP code directly into your theme files to display language switcher links:
        ```php
        if ( function_exists( 'msls_the_switcher' ) ) {
            msls_the_switcher();
        }
        ```
Review the [Multisite Language Switcher Website](http://msls.co/) for more information. Some [diagrams](https://github.com/lloc/Multisite-Language-Switcher/blob/master/Diagrams.md) are also available. 

## Developer Documentation

Reference material for developers extending or integrating with the plugin lives in the `docs/` directory:

*  [Public API Functions](docs/api.md) - the `msls_*` helper functions exposed for use in themes and other plugins.
*  [Hooks Reference](docs/hooks.md) - every action and filter the plugin emits, grouped by subsystem.
*  [Snippets & Examples](docs/snippets.md) - short, focused recipes for common integration tasks.

Credits for flag icons, banner artwork, and the full list of translators are maintained in [Acknowledgements & Translators](docs/acknowledgements.md).
