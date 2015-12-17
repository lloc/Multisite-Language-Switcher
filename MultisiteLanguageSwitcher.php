<?php

/*
Plugin Name: Multisite Language Switcher
Plugin URI: http://msls.co/
Description: A simple but powerful plugin that will help you to manage the relations of your contents in a multilingual multisite-installation.
Version: 1.0.8
Author: Dennis Ploetner
Author URI: http://lloc.de/
Text Domain: multisite-language-switcher
*/

/*
Copyright 2013  Dennis Ploetner  (email : re@lloc.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * MultisiteLanguageSwitcher
 *
 * @author Dennis Ploetner <re@lloc.de>
 */
if ( ! defined( 'MSLS_PLUGIN_VERSION' ) ) {
	define( 'MSLS_PLUGIN_VERSION', '1.0.8' );

	if ( ! defined( 'MSLS_PLUGIN_PATH' ) ) {
		define( 'MSLS_PLUGIN_PATH', plugin_basename( __FILE__ ) );
	}
	if ( ! defined( 'MSLS_PLUGIN__FILE__' ) ) {
		define( 'MSLS_PLUGIN__FILE__', __FILE__ );
	}

	add_action( 'plugins_loaded', array( 'MslsPlugin', 'init_i18n_support' ) );

	/**
	 * The Autoloader does all the magic when it comes to include a file
	 * @since 0.9.8
	 * @package Msls
	 */
	class MslsAutoloader {

		/**
		 * Static loader method
		 * @param string $class
		 */
		public static function load( $class ) {
			if ( 'Msls' == substr( $class, 0, 4 ) ) {
				require_once dirname( __FILE__ ) . '/includes/' . $class . '.php';
			}
		}

	}

	/**
	 * The autoload-stack could be inactive so the function will return false
	 */
	if ( in_array( '__autoload', (array) spl_autoload_functions() ) ) {
		spl_autoload_register( '__autoload' );
	}
	spl_autoload_register( array( 'MslsAutoloader', 'load' ) );

	/**
	 * Interface for classes which are to register in the MslsRegistry-instance
	 *
	 * get_called_class is just avalable in php >= 5.3 so I defined an interface here
	 * @package Msls
	 */
	interface IMslsRegistryInstance {

		/**
		 * Returnse an instance
		 * @return object
		 */
		public static function instance();

	}

	register_uninstall_hook( __FILE__, array( 'MslsPlugin', 'uninstall' ) );

	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		add_action( 'widgets_init', array( 'MslsPlugin', 'init_widget' ) );
		add_filter( 'locale', array( 'MslsPlugin', 'set_admin_language' ) );

		if ( is_admin() ) {
			add_action( 'admin_menu', array( 'MslsPlugin', 'init' ) );
			add_action( 'admin_menu', array( 'MslsAdmin', 'init' ) );

			add_action( 'load-post.php', array( 'MslsMetaBox', 'init' ) );

			add_action( 'load-post-new.php', array( 'MslsMetaBox', 'init' ) );

			add_action( 'load-edit.php', array( 'MslsCustomColumn', 'init' ) );
			add_action( 'load-edit.php', array( 'MslsCustomFilter', 'init' ) );

			add_action( 'load-edit-tags.php', array( 'MslsCustomColumnTaxonomy', 'init' ) );
			add_action( 'load-edit-tags.php', array( 'MslsPostTag', 'init' ) );

			if ( filter_has_var( INPUT_POST, 'action' ) ) {
				$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );

				if ( 'add-tag' == $action ) {
					add_action( 'admin_init', array( 'MslsPostTag', 'init' ) );
				}
				elseif ( 'inline-save' == $action ) {
					add_action( 'admin_init', array( 'MslsCustomColumn', 'init' ) );
				}
				elseif ( 'inline-save-tax' == $action ) {
					add_action( 'admin_init', array( 'MslsCustomColumnTaxonomy', 'init' ) );
				}
			}

			add_action( 'wp_ajax_suggest_posts', array( 'MslsMetaBox', 'suggest' ) );

			add_action( 'wp_ajax_suggest_terms', array( 'MslsPostTag', 'suggest' ) );
		}

		/**
		 * Filter for the_content()
		 *
		 * @package Msls
		 * @uses MslsOptions
		 * @param string $content
		 * @return string
		 */
		function msls_content_filter( $content ) {
			if ( ! is_front_page() && is_singular() ) {
				$options = MslsOptions::instance();
				if ( $options->is_content_filter() ) {
					$content .= msls_filter_string();
				}
			}
			return $content;
		}
		add_filter( 'the_content', 'msls_content_filter' );

		/**
		 * Create filterstring for msls_content_filter()
		 *
		 * @package Msls
		 * @uses MslsOutput
		 * @param string $pref
		 * @param string $post
		 * @return string
		 */
		function msls_filter_string( $pref = '<p id="msls">', $post = '</p>' ) {
			$obj    = new MslsOutput();
			$links  = $obj->get( 1, true, true );
			$output = __( 'This post is also available in %s.', 'multisite-language-switcher' );

			if ( has_filter( 'msls_filter_string' ) ) {
				/**
				 * Overrides the string for the output of the translation hint
				 * @since 1.0
				 * @param string $output
				 * @param array $links
				 */
				$output = apply_filters( 'msls_filter_string', $output, $links );
			}
			else {
				if ( count( $links ) > 1 ) {
					$last   = array_pop( $links );
					$output = sprintf(
						$output,
						sprintf(
							__( '%s and %s', 'multisite-language-switcher' ),
							implode( ', ', $links ),
							$last
						)
					);
				}
				elseif ( 1 == count( $links ) ) {
					$output = sprintf(
						$output,
						$links[0]
					);
				}
				else {
					$output = '';
				}
			}
			return( ! empty( $output ) ? $pref . $output . $post : '' );
		}

		/**
		 * Get the output for using the links to the translations in your code
		 *
		 * @package Msls
		 * @param array $arr
		 * @return string
		 */
		function get_the_msls( $arr = array() ) {
			$obj = MslsOutput::init()->set_tags( (array) $arr );
			return( sprintf( '%s', $obj ) );
		}
		add_shortcode( 'sc_msls', 'get_the_msls' );

		/**
		 * Output the links to the translations in your template
		 *
		 * You can call this function directly like that
		 *
		 *     if ( function_exists ( 'the_msls' ) )
		 *         the_msls();
		 *
		 * or just use it as shortcode [sc_msls]
		 *
		 * @package Msls
 	 	 * @uses get_the_msls
		 * @param array $arr
		 */
		function the_msls( $arr = array() ) {
			echo get_the_msls( $arr ); // xss ok
		}

		/**
		 * Help searchengines to index and to serve the localized version with
		 * rel="alternate"-links in the html-header
		 */
		function msls_head() {
			$blogs  = MslsBlogCollection::instance();
			$mydata = MslsOptions::create();
			foreach ( $blogs->get_objects() as $blog ) {
				$language = $blog->get_language();
				$url      = $mydata->get_current_link();
				$current  = ( $blog->userblog_id == MslsBlogCollection::instance()->get_current_blog_id() );
				$title = $blog->get_description();

				if ( ! $current ) {
					switch_to_blog( $blog->userblog_id );

					if ( 'MslsOptions' != get_class( $mydata ) && ( is_null( $mydata ) || ! $mydata->has_value( $language ) ) ) {
						restore_current_blog();
						continue;
					}
					$url = $mydata->get_permalink( $language );
					$title = $blog->get_description();

					restore_current_blog();
				}

				if ( has_filter( 'msls_head_hreflang' ) ) {
					/**
					 * Overrides the hreflang value
					 * @since 0.9.9
					 * @param string $language
					 */
					$hreflang = (string) apply_filters( 'msls_head_hreflang', $language );
				}
				else {
					$hreflang = $blog->get_alpha2();
				}

				printf(
					'<link rel="alternate" hreflang="%s" href="%s" title="%s" />',
					$hreflang,
					$url,
					esc_attr( $title )
				);
				echo "\n";
			}
		}
		add_action( 'wp_head', 'msls_head' );

	}
	else {

		/**
		 * Prints a message that the Multisite Language Switcher needs an
		 * active multisite to work properly.
		 */
		function plugin_needs_multisite() {
			MslsPlugin::message_handler(
				__( 'The Multisite Language Switcher needs the activation of the multisite-feature for working properly. Please read <a onclick="window.open(this.href); return false;" href="http://codex.wordpress.org/Create_A_Network">this post</a> if you don\'t know the meaning.', 'multisite-language-switcher' )
			);
		}
		add_action( 'admin_notices', 'plugin_needs_multisite' );

	}
}
