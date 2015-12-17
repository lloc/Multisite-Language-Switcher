=== Multisite Language Switcher ===

Contributors: realloc
Donate link: http://www.greenpeace.org/international/
Tags: multilingual, multisite, language, switcher, international, localization, i18n
Requires at least: 3.6.1
Tested up to: 4.4
Stable tag: 1.0.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple, powerful and easy to use plugin that will help you to manage multilingual content in a multisite WordPress installation. 

== Description ==

A simple, powerful and easy to use plugin that will add multilingual support to a WordPress [multisite](http://codex.wordpress.org/Create_A_Network) 
installation, i.e. multiple subdomains or folders (if you need to set up multiple sites across multiple domains, you'll also want to use the [WordPress MU 
Domain Mapping](http://wordpress.org/extend/plugins/wordpress-mu-domain-mapping/) plugin as well - as long as the domains are all hosted on the same server.).

The Multisite Language Switcher enables you to manage translations of posts, pages, custom post types, categories, tags and custom taxonomies.

The plugin uses flag-icons from [FamFamFam](http://famfamfam.com). Thanks to Mark James for his great work. In addition I would like to thank [Jürgen Mayer](
http://blog.jrmayer.co/) for creating the plugin's banner.
 
== Installation ==

* Download the plugin and uncompress it with your preferred unzip programme
* Copy the entire directory in your plugin directory of your WordPress blog (/wp-content/plugins)
* Activate the plugin in your plugin administration page (by the network administrator on all the blogs or by the root blog administrator for each particular blog).
* You need to activate the plugin once in each blog, set the configuration in Settings -> Multisite Language Switcher

Now you can:

* connect your translated pages and posts in Posts -> Edit or Page -> Edit
* connect your translated categories and tags in Posts -> Categories or Post -> Tags
* connect your Custom Post Types and Custom Taxonomies across languages
* use the widget, the shortcode [sc_msls] and/or a content_filter which displays a hint to the user if a translation is available
* optionally you can place the code `<?php if ( function_exists( 'the_msls' ) ) the_msls(); ?>` directly in your theme files

Review the [Multisite Language Switcher Website](http://msls.co/) for more information.

== Frequently Asked Questions ==

= I have no language options in the General settings. =

You might read first [WordPress in your language](http://codex.wordpress.org/WordPress_in_Your_Language).

= But I'd like the interface to stay in English. =

You can choose the language of every website and the dashboard in the settings page of the plugin.

= Do I really need a multisite? =

It's up to you - of course. But yes, if you want to use the Multisite Language Switcher.

= How can I automatically redirect users based on the browser language? =

The Multisite Language Switcher does not redirect the users automatically. I'm not sure if the plugin should do that. You might check out this [jQuery plugin](
https://github.com/danieledesantis/jquery-language-detection) or [this approach with a theme](https://github.com/oncleben31/Multisite-Language-Switcher-Theme) 
if you need such functionality.

= How can I add the Multisite Language Switcher to the nav-menu of my blog? =

Please check the add-on [MslsMenu](https://wordpress.org/plugins/mslsmenu/) out.

= I want to have the languages in an HTML select. How can I do that? =

Please check the add-on [MslsSelect](https://wordpress.org/plugins/mslsselect/) out.

= I don't want to upload the same media files for every site. What can I do? =

You could try the plugin [Network Shared Media](http://wordpress.org/plugins/network-shared-media/). It adds a new tab to the "Add Media" window, allowing you to access the media files in the other sites in your multisite.

= Is there a function I can call to get the language of the page the user is currently viewing? =

Yes, you can get the language like that

`$blog     = MslsBlogCollection::instance()->get_current_blog();
$language = $blog->get_language();`

= How can I move from WPML to MSLS? =

There is a [plugin](http://wordpress.org/plugins/wpml2wpmsls/) which comes handy in here.

== Screenshots ==

1. Plugin configuration
2. Posts list
3. Edit post
4. Widget

== Changelog ==

= 1.0.8 =
* Bugfix for issue [American English doesn't work with WordPress 4.3](https://wordpress.org/support/topic/american-english-doesnt-work-with-wordpress-43?replies=12#post-7791218)

= 1.0.7 =
* text domain is now 'multisite-language-switcher', translation files moved to refelect this change

= 1.0.6 =
* language files for Bulgarian (bg_BG) by Vencislav Raev added
* admin options page heading is now h1 instead of h2

= 1.0.5 =
* Filter 'msls_meta_box_render_select_hierarchical' closes issue [64](https://github.com/lloc/Multisite-Language-Switcher/issues/64)
* Bugfix for issue [73](https://github.com/lloc/Multisite-Language-Switcher/issues/73)
* language files for Norwegian (nb_NO) by Ibrahim Qraiqe added
* Bugfix for issue [Multisite Language Switcher Options is empty](https://wordpress.org/support/topic/multisite-language-switcher-options-is-empty)

= 1.0.4 =
* language files for Arabic (ar) by Mohamed Elwan added

= 1.0.3 =
* Bugfix: alternate hreflang for the current blog was empty
* Filter: 'msls_options_get_flag_icon' introduced
* Shortcode [sc_msls] added

= 1.0.2 =
* Bugfix: term links were damaged when term_base option were used
* Bugfix: en_GB & en_US both displayed as 'English' in Settings > MLS >Blog & Admin language
* Filter 'msls_meta_box_render_input_button' introduced

= 1.0.1 =
* Bugfix: filter internal types from get_post_stati

= 1.0 =
* you can choose now your frontend/backend language from the installed languages in the plugins settings
* support for 25 languages
* numerous enhancements and fixes (coding style, sql cache, performance, api docs, unit tests ...)

[...]

= 0.9.9 =
* Option to transform the dropdowns for choosing the connections into text-inputs with jquery-ui autocomplete
* Plugin cleans up the option-tables now when uninstalled
* Problem with multiple admin resolved, there is now a "Reference User"-option available
* a lot of new filters/action, more on this later in the Wiki
* now you can use `<?php if ( function_exists( 'msls_filter_string' ) ) msls_filter_string(); ?>` if you'ld like to print the "hint"-string anywhere in your templates
* language files for ca_ES by Joan López, cs_CZ by Rastr and bn_BD by Md. Nazmul Huda added
* MslsCustomFilter-Class by Maciej Czerpiński
* and tons of other minor improvements

[...]

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

[...]

= 0.1 =
* first version

== Translators ==

Thanks to all translators for their great work.

* German (de_DE) - [Dennis Ploetner](http://lloc.de/)
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
* Japanese (ja) - [ThemeBoy](http://themeboy.com/)
* Swedish (sv_SE) - [Erik Bernskiold](http://www.bernskioldmedia.com/)
* Traditional & Simplified Chinese (zh_CN & zh_TW) - DrBenson
* Arabic (ar) - Mohamed Elwan
* Norwegian (nb_NO) - Ibrahim Qraiqe
* Bulgarian (bg_BG) - [Vencislav Raev](http://www.catblue.net/)

If you have created your own language pack, or have an update of an existing one, you can [send me](mailto:re@lloc.de) your gettext PO and MO so that I can
bundle it into the Multisite Language Switcher. You can download the latest POT file
[from here](http://plugins.svn.wordpress.org/multisite-language-switcher/trunk/languages/default.pot).
