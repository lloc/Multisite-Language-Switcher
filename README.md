# Multisite Language Switcher

A simple but powerful plugin that will help you to manage the relations of posts, pages, custom post types, categories, tags and custom taxonomies in your multilingual multisite-installation.

The plugin is using the flag-icons from [FamFamFam](http://famfamfam.com). Thanks to Mark James for his great work. In addition, I would like to thank [Jürgen Mayer](http://designetage.com/) for making the banner of this plugin.

## Translators

*  German (de_DE) - [Dennis Ploetner](http://www.urlaub-und-reisen.net) 
*  Italian (it_IT) - [Antonella Cucinelli](http://www.freely.de)
*  Dutch (nl_NL) - [Alexandra Kleijn](http://www.buurtaal.de/) 
*  Brazillian Portuguese (pt_BR) - [Victor](http://www.coolweb.com.br/)
*  Spanish (es_ES) - [Andreas Breitschopp](http://www.ab-weblog.com/en/) 
*  French (fr_FR) - [Andreas Breitschopp](http://www.ab-tools.com/en/)
*  Russian (ru_RU) - [Andrey Vystavkin](http://j4vk.com/)
*  Ukrainian (uk) - [Victor Melnichenko](http://worm.org.ua/)
*  Croatian (hr_HR) - [Brankec69](https://github.com/Brankec69)
*  Romanian (ro_RO) - [Armand K](http://caveatlector.eu/)

If you have created your own language pack, or have an update of an existing one, you can [send me](mailto:re@lloc.de) your gettext PO and MO so that I can bundle it into the _Multisite Language Switcher_. You can download the latest POT file [from here](http://plugins.svn.wordpress.org/multisite-language-switcher/trunk/languages/default.pot).

## Installation

*  download the plugin
*  uncompress it with your preferred unzip programme
*  copy the entire directory in your plugin directory of your wordpress blog (/wp-content/plugins)
*  activate the plugin in your plugin page
*  set some configuration in Options -> Multisite Language Switcher
*  set the relations of your pages and posts in Posts -> Edit or Page -> Edit and custom post types, categories and tags as well 
*  now you can use the widget and/or the content_filter which displays a hint if a translation is available
*  optionally you can use a line like `<?php if ( function_exists( 'the_msls' ) ) the_msls(); ?>` directly in your theme-files
