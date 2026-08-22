=== Multisite Language Switcher ===

Contributors: realloc, lucatume
Donate link: https://www.greenpeace.org/international/
Tags: multilingual, multisite, language, switcher, localization
Requires at least: 6.1
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 3.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple, powerful and easy-to-use plugin that will help you to manage multilingual content in a multisite WordPress installation.

== Description ==

A simple, powerful, and user-friendly plugin that adds multilingual support to your [WordPress multisite](https://wordpress.org/documentation/article/create-a-network/) installation, whether using multiple subdomains or folders. Multisite Language Switcher allows you to effortlessly manage translations for posts, pages, custom post types, categories, tags, and custom taxonomies.

The plugin uses flag icons from [FamFamFam](http://famfamfam.com). Thanks to Mark James for his great work. In addition, I would like to thank [Jürgen Mayer](https://creativpin.com) for creating the plugin's banner.

Please, don't forget to [rate this plugin](https://wordpress.org/support/plugin/multisite-language-switcher/reviews/)! :-)

== Installation ==

* Use the WordPress admin to install the plugin from there or
* Download the plugin and uncompress it with your preferred unzip programme and copy the entire directory in the plugin directory of your WordPress blog (/wp-content/plugins)
* Activate the plugin in your plugin administration page (by the network administrator on all the blogs or by the blog administrator for each particular blog).
* After activation, configure the plugin once in each blog under `Settings` -> `Multisite Language Switcher`

Now you can:

* Connect your translated pages and posts in `Posts` -> `Edit` or `Pages` -> `Edit`
* Connect your translated categories and tags in `Posts` -> `Categories` or `Posts` -> `Tags`
* connect your Custom Post Types and Custom Taxonomies across languages
* create a missing translation in one step - straight from the editor metabox, or from the `Add from Translation` submenu, which lists everything that is not translated yet and can create translations in bulk (Quick Create, new in 3.0)
* use the widget, the Gutenberg block, the shortcode [sc_msls] and/or a content_filter which displays a hint to the user if a translation is available
* you can find also a shortcode for the widget [sc_msls_widget]
* optionally you can place the code `<?php if ( function_exists( 'msls_the_switcher' ) ) msls_the_switcher(); ?>` directly in your theme files

Review the [Multisite Language Switcher Website](https://msls.co/) for more information.
Developers will find the API functions, every action and filter, and a set of integration
snippets in the [developer documentation](https://github.com/lloc/Multisite-Language-Switcher/tree/master/docs).

== Frequently Asked Questions ==

= I have no language options in the General settings. =

You might read first [WordPress in your language](http://codex.wordpress.org/WordPress_in_Your_Language).

= But I'd like the interface to stay in English. =

You can choose the language of the dashboard in the settings of your user profile.

= Do I need a multisite? =

It's up to you - of course. But yes, if you want to use the Multisite Language Switcher.

= How can I automatically redirect users based on the browser language? =

The Multisite Language Switcher does not redirect the users automatically. I'm not sure if the plugin should do that. You might check out this [jQuery plugin](
https://github.com/danieledesantis/jquery-language-detection) or [this approach with a theme](https://github.com/oncleben31/Multisite-Language-Switcher-Theme)
if you need such functionality.

= How can I add the Multisite Language Switcher to the nav menu of my blog? =

Please check the add-on [MslsMenu](https://wordpress.org/plugins/mslsmenu/) out.

= I want to have the languages in an HTML select. How can I do that? =

Please check the add-on [MslsSelect](https://wordpress.org/plugins/mslsselect/) out.

= Can I call a function to get the language of the page the user is viewing? =

Yes, you should use the WordPress API function `get_locale()` but you could also use code like that

`$blog     = msls_blog_collection()->get_current_blog();
$language = $blog->get_language();`

The class behind it moved to `lloc\Msls\Blog\Collection` in version 3.0.0. The old name
`lloc\Msls\MslsBlogCollection` still resolves, so existing code keeps working.

= If I have another question, where can I ask? =

Please visit the [MSLS website](https://msls.co/) or use the [WordPress support forum](https://wordpress.org/support/plugin/multisite-language-switcher) for more information.

== Screenshots ==

1. Plugin configuration with labels
2. Plugin configuration with flag
3. Posts list with label
4. Post list with flag
5. Edit post with label and select2
6. Edit post with flag and dropdown
7. Non-styled output of the widget, block and shortcode

== Changelog ==

= 3.0.0 =

* New: Quick Create - create the translated post straight from the editor metabox, or pick a source post on the new `Add from Translation` submenu (single and bulk). Backed by a REST endpoint and switchable in the settings.
* New: `msls_quick_create_capability` lets integrations override the Quick Create permission checks. Additional filters cover the post data, the inserted post, the REST response, the untranslated-posts list and the mapped taxonomy terms.
* New: filter hooks for the AJAX suggest results of the post and term metaboxes.
* New: a developer reference in `docs/` - public API, every hook, integration snippets and the Playwright setup.
* Changed: the `lloc\Msls\` namespace is split into per-concern sub-namespaces. Every pre-3.0 class name (`MslsOptions`, `MslsLink`, `MslsOutput`, ...) keeps working through a backwards-compatibility alias, so add-ons and custom code do not need changes.
* Changed: the public helper functions moved to `includes/api.php`, and the `$attr` argument of `msls_get_switcher()` is now optional.
* Fixed: add-ons such as MslsMenu and MslsSelect could be silently disabled because the plugin registered its classes too late. Aliases and API functions are now available the moment the plugin file is loaded.
* Fixed: authorization is checked on the destination post during content import, and the ContentImporter permission and post type checks were corrected.
* Fixed: no `home_url()` fallback for taxonomy and query archives anymore.
* Fixed: broken links on the page for the latest posts.
* Fixed: several issues in the blog collection.
* Internal: strict typing throughout, PHPStan level 8 clean, `ABSPATH` guards, Plugin Check and PHPCS findings addressed, a wp-env based local multisite and a Playwright end-to-end suite.

= 2.10.1 =

* Fixed: deprecated function warning pointed to a non-existent function.
* Documentation: README.md code snippets reflect the new function names.

= 2.10.0 =

* New: prefixed public helper functions (`msls_get_*`, `msls_the_switcher`) with deprecation shims for the legacy names.
* New: action hook names are exposed as constants for safer programmatic use.
* Accessibility: `aria-current="page"` on the active language link.
* Fixed: path-related issue affecting asset/loader resolution.

The full history is kept in the separate [Changelog](https://github.com/lloc/Multisite-Language-Switcher/blob/master/Changelog.md).

== Upgrade Notice ==

= 3.0.0 =

Major release. The class structure moved into sub-namespaces, but every pre-3.0 class name
still resolves through a compatibility alias, so add-ons, themes and snippets keep working.
Requirements are unchanged (PHP 7.4, WordPress 6.1). Clear your opcode cache after updating.

== Translators ==

Thanks to all the translators for their great work.

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
* Mexican Spanish (es_MX) - [Fernando Mata](https://fernandomata.mx/)

You can translate this plugin on [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/multisite-language-switcher/), or if you prefer and have created your language pack, or have an update of an existing one, you can [send me](mailto:re@lloc.de) your Gettext PO and MO so that I can
bundle it into the Multisite Language Switcher. You can download the latest POT file
[from here](http://plugins.svn.wordpress.org/multisite-language-switcher/trunk/languages/default.pot).
