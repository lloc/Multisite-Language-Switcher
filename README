= Multisite Language Switcher =

A simple but powerful plugin that will help you to manage the relations of posts/pages/categories/... in your multisite-multilingual-installation.

== Description ==

A simple but powerful plugin that will help you to manage the relations of posts, pages, categories and tags in your multisite-multilingual-installation.

The plugin is using the flag-icons from [http://famfamfam.com FamFamFam]. Thanks to Mark James for his great work.

=== Translators ===
  * German (de_DE) - [http://www.urlaub-und-reisen.net Dennis Ploetner]
  * Italian (it_IT) - [http://www.freely.de Antonella Cucinelli]
  * Dutch (nl_NL) - [http://www.buurtaal.de/ Alexandra Kleijn]
  * Brazillian Portuguese (pt_BR) - [http://www.coolweb.com.br/ Victor]
  * Spanish (es_ES) - [http://www.ab-weblog.com/en/ Andreas Breitschopp]

If you have created your own language pack, or have an update of an existing one, you can [mailto:re@lloc.de send me] your gettext PO and MO so that I can bundle it into the Multisite Language Switcher. You can download the latest POT file [http://plugins.svn.wordpress.org/multisite-language-switcher/trunk/languages/default.pot from here].

== Installation ==

  * download the plugin
  * uncompress it with your preferred unzip programme
  * copy the entire directory in your plugin directory of your wordpress blog (/wp-content/plugins)
  * activate the plugin in your plugin page
  * set some configuration in Options -> Multisite Language Switcher
  * set the relations of your pages and posts in Posts -> Edit or Page -> Edit
  * now you can use the widget and/or the content_filter which displays a hint if a translation is available
  * optionally you can use a line like `<?php if (function_exists("the_msls")) the_msls(); ?>` directly in your theme-files

== Changelog ==

=== 0.6.8 ===
  * bugfix: str_replace problem with 4th parameter

=== 0.6.7 ===
  * bugfix: get_term_link seems to fail if there is a custom category_base or tag_base defined
  * bugfix: get_term_link expects term_id as integer
  * bugfix: do not include blogs without configuration
  * bugfix: fatal error if there is no configuration

=== 0.6.6 ===
  * language files for es_ES added

=== 0.6.5 ===
  * language files for pt_BR added

=== 0.6.4 ===
  * bugfix: strange behaviour of the sidebar-widget corrected

=== 0.6.3 ===
  * bugfix: there was a problem with restore_current_blog() and more then 2 languages
  * bugfix: problem under some circumstancess with the content_filter

=== 0.6.2 ===
  * bugfix: sometimes no flag was shown because WPLANG could be empty for en_US-Blogs in the network

=== 0.6.1 ===
  * bugfix: notice when MslsLink::$txt was requested for output of the link-title

=== 0.6 ===
  * new: relations between categories and tags in different languages

=== 0.5 ===
  * language files for nl_NL added

=== 0.4 ===
  * widget added
  * hint for available translations as filter of the_content added
  * bugfix: `$this->options->before_output`, `$this->options->after_output`

=== 0.3 ===
  * new display-option added
  * optimization/refactoring

=== 0.2 ===
  * bugfix: Showstopper in `MslsMain::__construct ()`

=== 0.1 ===
  * first version
