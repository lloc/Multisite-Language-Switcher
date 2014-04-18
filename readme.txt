=== Multisite Language Switcher ===

Contributors: realloc
Donate link: http://www.greenpeace.org/international/
Tags: multilingual, multisite, language, switcher, international, localization, i18n
Requires at least: 3.2.1
Tested up to: 3.9
Stable tag: 0.9.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple, powerful and easy to use plugin that will help you to 
manage multilingual content in a multisite WordPress installation.

== Description ==

A simple, powerful and easy to use plugin that will add 
multilingual support to a WordPress 
[multisite](http://codex.wordpress.org/Create_A_Network) 
installation, i.e. multiple subdomains or folders (if you need to 
set up multiple sites across multiple domains, you'll also want to 
use the
[WordPress MU Domain Mapping](http://wordpress.org/extend/plugins/wordpress-mu-domain-mapping/)
plugin as well - as long as the domains are all hosted on the 
same server.).

The Multisite Language Switcher enables you to manage translations of 
posts, pages, custom post types, categories, tags and custom taxonomies.

The plugin uses flag-icons from [FamFamFam](http://famfamfam.com). 
Thanks to Mark James for his great work. In addition I would like to 
thank [Jürgen Mayer](http://blog.jrmayer.co/) for creating the plugin's 
banner. 

= Translators =
* German (de_DE) - [Dennis Ploetner](http://www.urlaub-und-reisen.net/)
* Italian (it_IT) - [Antonella Cucinelli](http://www.freely.de/it/)
* Dutch (nl_NL) - [Alexandra Kleijn](http://www.buurtaal.de/)
* Brazillian Portuguese (pt_BR) - [Coolweb](http://www.coolweb.com.br/)
* Spanish (es_ES) - [Andreas Breitschopp](http://www.ab-weblog.com/en/)
* French (fr_FR) - [Andreas Breitschopp](http://www.ab-tools.com/en/)
* Russian (ru_RU) - [Andrey Vystavkin](http://j4vk.com/)
* Ukrainian (uk) - [Victor Melnichenko](http://worm.org.ua/)
* Croatian (hr_HR) - [Brankec69](https://github.com/Brankec69) 
* Romanian (ro_RO) - [Armand K](http://caveatlector.eu/)
* Polish (pl_PL) - [Kamil Frejlich](http://www.mojito-networks.com/)
* Lithuanian (lt_LT) - Ovidijus Pukys
* Catalan (ca_ES) - Joan López
* Czech (cs_CZ) - Rastr
* Hungarian (hu_HU) - RobiG
* Georgian (ka_GE) - [Jas Saran](http://www.gwebpro.com/)
* Greek (el) - [Christoforos Aivazidis](http://www.aivazidis.org/)
* Serbian (sr_RS) - [Web Hosting Hub](http://www.webhostinghub.com/)
* Turkish (tr) - Alican Cakil
* Armenian (hy_AM) - Yeghishe Nersisyan
* Bengali (bn_BD) - Md. Nazmul Huda

If you have created your own language pack, or have an update of an 
existing one, you can [send me](mailto:re@lloc.de) your gettext PO 
and MO so that I can bundle it into the Multisite Language Switcher. 
You can download the latest POT file
[from here](http://plugins.svn.wordpress.org/multisite-language-switcher/trunk/languages/default.pot).

== Installation ==

* Download the plugin and uncompress it with your preferred unzip programme 
* Copy the entire directory in your plugin directory of your WordPress blog (/wp-content/plugins) 
* Activate the plugin in your plugin administration page 
* Set the configuration in Options -> Multisite Language Switcher 

Now you can: 

* connect your translated pages and posts in Posts -> Edit or Page -> Edit 
* connect your translated categories and tags in Posts -> Categories or Post -> Tags
* connect your Custom Post Types and Custom Taxonomies across languages
* use a widget and/or a content_filter which displays a hint to the user if a translation is available 
* optionally you can place the code `<?php if ( function_exists( 'the_msls' ) ) the_msls(); ?>` directly in your theme files 

Review the
[Multisite Language Switcher Wiki](https://github.com/lloc/Multisite-Language-Switcher/wiki)
for more information.

== Frequently Asked Questions ==

= Is there a function I can call to get the language of the page the user is currently viewing? =

Yes, you can get the language like that

`$blog     = MslsBlogCollection::instance()->get_current_blog();
$language = $blog->get_language();`

= I have no language options in the General settings. =

You might read first [WordPress in your language](http://codex.wordpress.org/WordPress_in_Your_Language).

= But I'd like the interface to stay in English. =

You could check out the plugin [Native Dashboard](http://wordpress.org/extend/plugins/wp-native-dashboard/).

= Do I really need a multisite? =

It's up to you - of course. Yes, if you want to use the Multisite Language Switcher.

== Screenshots ==

1. Plugin configuration
2. Posts list
3. Edit post
4. Widget

== Changelog ==

= 0.9.9.2 =
* Bugfix: Format of the widget-title was not correct
* Update of the language-files for Italian and German

= 0.9.9.1 =
* Bugfix: Widget was defect

= 0.9.9 =
* Option to transform the dropdowns for choosing the connections into text-inputs with jquery-ui autocomplete
* Plugin cleans up the option-tables now when uninstalled
* Problem with multiple admin resolved, there is now a "Reference User"-option available
* a lot of new filters/action, more on this later in the Wiki
* now you can use `<?php if ( function_exists( 'msls_filter_string' ) ) msls_filter_string(); ?>` if you'ld like to print the "hint"-string anywhere in your templates
* language files for ca_ES by Joan López, cs_CZ by Rastr and bn_BD by Md. Nazmul Huda added
* MslsCustomFilter-Class by Maciej Czerpiński
* and tons of other minor improvements

= 0.9.8.2 =
* lots of language files added

= 0.9.8.1 =
* Fix for "All inone event calendar"

= 0.9.8 =
* Fix of the "MslsOptionsQueryDay.php"-bug
* language files for es_ES updated by [José Luis Pajares](http://gelo.tv/)

= 0.9.7 =
* further bugfixes and enhancements

= 0.9.6 =
* a lot of bugfixes and enhancements
* some modifications - how the meta box handles hierarchical post types - by [Joost de Keijzer](http://dekeijzer.org/)
* behaviour - how the plugin collects the blogs - changed
* language files for ro_RO added

= 0.9.5 =
* new: Support for author- and date-archives
* language files for hr_HR added
* some modifications of the image path handling by [Tobias Bäthge](http://tobias.baethge.com/)

= 0.9.4 =
* language files for uk added

= 0.9.3 =
* language files for ru_RU added

= 0.9.2 =
* new: _msls_output_get_-filter for customizing the output
* new: link of the current language is marked with `class="current_language"`
* new: index.php in all directories redirects to HTTP_HOST
* bugfix: admin metabox and the link to a _new_ page
* bugfix: "Display link to the current language" was ignored when option "Show only links with a translation" is checked as well
* bugfix: _msls_content_filter_ should work only when display a post_type
* _msls_blog_collection_get_-filter deleted

= 0.9.1 =
* bugfix: broken methods in MslsAdmin

= 0.9 =
* new: support for custom post types
* new: apply_filters( 'msls_blog_collection_construct', $arr ); in MslsBlogCollection::__construct();
* bugfix: msls_blog_collection_get
* language files for fr_FR and es_ES updated

[...]

= 0.1 =
* first version
