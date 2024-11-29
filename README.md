# Multisite Language Switcher

A simple, powerful, and user-friendly plugin that adds multilingual support to your [WordPress multisite](https://wordpress.org/documentation/article/create-a-network/) installation, whether using multiple subdomains or folders. Multisite Language Switcher allows you to effortlessly manage translations for posts, pages, custom post types, categories, tags, and custom taxonomies.

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/multisite-language-switcher.svg)](https://wordpress.org/plugins/multisite-language-switcher/)
[![License](https://img.shields.io/badge/license-GPL_v2%2B-blue.svg)](http://opensource.org/licenses/GPL-2.0)
[![WordPress Tested](https://img.shields.io/wordpress/v/multisite-language-switcher.svg)](https://wordpress.org/plugins/multisite-language-switcher/)
[![codecov](https://codecov.io/gh/lloc/Multisite-Language-Switcher/graph/badge.svg?token=IlD4bX4KZ4)](https://codecov.io/gh/lloc/Multisite-Language-Switcher)

## Where to get the plugin

[Download](http://downloads.wordpress.org/plugin/multisite-language-switcher.zip) the latest stable from the [WordPress Plugin Directory](https://wordpress.org/plugins/multisite-language-switcher/), and please remember to give this plugin [a five-star rating](https://wordpress.org/support/plugin/multisite-language-switcher/reviews/#new-post).

_Please note that while the master branch on GitHub is intended to be stable, it is not recommended for production use. Instead, please use the [official releases](https://github.com/lloc/Multisite-Language-Switcher/releases) for deployment._

If you plan to use the GitHub repository on a server, don't forget to run `composer install --no-dev`.

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
    *  Use the [sc_msls] shortcode in the classic editor.
    *  Set the content filter to display a hint to users when a translation is available.

*  Optional Theme Integration:
    *  Insert the following PHP code directly into your theme files to display language switcher links:
        ```php
        if ( function_exists( 'the_msls' ) ) {
            the_msls();
        }
        ```
Review the [Multisite Language Switcher Website](http://msls.co/) for more information. Some [diagrams](https://github.com/lloc/Multisite-Language-Switcher/blob/master/Diagrams.md) are also available. 

## Acknowledgements

The plugin uses flag-icons from [FamFamFam](http://famfamfam.com).
Thanks to Mark James for his great work. In addition I would like to
thank [Jürgen Mayer](https://creativpin.com) for creating the plugin's
banner.

## Translators

Thanks to all translators for their great work.

*  German (de_DE) - [Dennis Ploetner](http://lloc.de/) 
*  Italian (it_IT) - [Antonella Cucinelli](http://www.freely.de/)
*  Dutch (nl_NL) - [Alexandra Kleijn](http://www.buurtaal.de/) 
*  Brazillian Portuguese (pt_BR) - [Victor](http://www.coolweb.com.br/)
*  Spanish (es_ES) - [Andreas Breitschopp](http://www.ab-weblog.com/en/) 
*  French (fr_FR) - [Andreas Breitschopp](http://www.ab-tools.com/en/)
*  Russian (ru_RU) - [Andrey Vystavkin](http://j4vk.com/)
*  Ukrainian (uk) - [Victor Melnichenko](http://worm.org.ua/)
*  Croatian (hr_HR) - [Brankec69](https://github.com/Brankec69)
*  Romanian (ro_RO) - [Armand K](http://caveatlector.eu/)
*  Polish (pl_PL) - [Kamil Frejlich](http://www.mojito-networks.com/)
*  Lithuanian (lt_LT) - Ovidijus Pukys
*  Catalan (ca_ES) - Joan López
*  Czech (cs_CZ) - Rastr
*  Hungarian (hu_HU) - RobiG
*  Georgian (ka_GE) - [Jas Saran](http://www.gwebpro.com/)
*  Greek (el) - [Christoforos Aivazidis](http://www.aivazidis.org/)
*  Serbian (sr_RS) - [Web Hosting Hub](http://www.webhostinghub.com/)
*  Turkish (tr) - Alican Cakil
*  Armenian (hy_AM) - Yeghishe Nersisyan
*  Bengali (bn_BD) - Md. Nazmul Huda
*  Japanese (ja) - [ThemeBoy](http://themeboy.com/)
*  Swedish (sv_SE) - [Erik Bernskiold](http://www.bernskioldmedia.com/)
*  Traditional & Simplified Chinese (zh_CN & zh_TW) - DrBenson
*  Arabic (ar) - Mohamed Elwan
*  Norwegian (nb_NO) - Ibrahim Qraiqe
*  Bulgarian (bg_BG) - [Vencislav Raev](http://www.catblue.net/)

You can translate this plugin on [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/multisite-language-switcher/), or if you prefer and have created your own language pack, or have an update of an 
existing one, you can [send me](mailto:re@lloc.de) your gettext PO 
and MO so that I can bundle it into the Multisite Language Switcher. 
You can download the latest POT file
[from here](https://github.com/lloc/Multisite-Language-Switcher/blob/master/languages/default.pot).
