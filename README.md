# Multisite Language Switcher

_Please keep in mind that the version of the Multisite Language Switcher at GitHub is a work in progress._

**Download the [latest stable from the WordPress Plugin Directory](http://downloads.wordpress.org/plugin/multisite-language-switcher.zip).**
 
[![Build Status](https://api.travis-ci.org/lloc/Multisite-Language-Switcher.png)](https://api.travis-ci.org/lloc/Multisite-Language-Switcher)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lloc/Multisite-Language-Switcher/badges/quality-score.png?s=a2e5dbac2690bbd427f2d48b84473482e7e24fdb)](https://scrutinizer-ci.com/g/lloc/Multisite-Language-Switcher/)

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

## Installation

*  Download the plugin and uncompress it with your preferred unzip programme
*  Copy the entire directory in your plugin directory of your WordPress blog (/wp-content/plugins)
*  Activate the plugin in your plugin administration page (by the network administrator on all the blogs or by the root blog administrator for each particular blog).
*  You need to activate the plugin once in each blog, set the configuration in Options -> Multisite Language Switcher

Now you can: 

*  connect your translated pages and posts in Posts -> Edit or Page -> Edit
*  connect your translated categories and tags in Posts -> Categories or Post -> Tags
*  connect your Custom Post Types and Custom Taxonomies across languages
*  use the widget, the shortcode [sc_msls] and/or a content_filter which displays a hint to the user if a translation is available
*  optionally you can place the PHP code `<?php if ( function_exists( 'the_msls' ) ) the_msls(); ?>` directly in your theme files

Review the [Multisite Language Switcher Website](http://msls.co/) for more information.

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

If you have created your own language pack, or have an update of an 
existing one, you can [send me](mailto:re@lloc.de) your gettext PO 
and MO so that I can bundle it into the Multisite Language Switcher. 
You can download the latest POT file
[from here](https://github.com/lloc/Multisite-Language-Switcher/blob/master/languages/default.pot).
