<?php
/**
 * MslsPlugin
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Provides functionalities for activation an deactivation
 * @package Msls
 */
class MslsPlugin {

	/**
	 * Register widget
	 * 
	 * The widget will only be registered if the current blog is not 
	 * excluded in the configuration of the plugin.
	 * @return boolean
	 */
	public static function init_widget() {
		if ( ! MslsOptions::instance()->is_excluded() ) {
			register_widget( 'MslsWidget' );
			return true;
		}
		return false;
	}

	/**
	 * Load textdomain
	 * 
	 * The method will be executed allways on init because we have some
	 * translatable string in the frontend too.
	 * @return boolean
	 */
	public static function init_i18n_support() {
		return load_plugin_textdomain(
			'msls',
			false,
			dirname( MSLS_PLUGIN_PATH ) . '/languages/'
		);
	}

	/**
	 * Set the admin language
	 * Callback for 'locale' hook
	 * @param string $locale
	 * @return string
	 */
	public static function set_admin_language( $locale ) {
		if ( is_admin() ) {
			$code   = MslsOptions::instance()->admin_language;
			if ( ! empty( $code ) ) {
				return $code;
			}
		}
		return $locale;
	}

	/**
	 * Message handler
	 * 
	 * Prints a message box to the screen.
	 * @param string $message
	 * @param string $css_class
	 * @return boolean
	 */
	public static function message_handler( $message, $css_class = 'error' ) {
		if ( ! empty( $message ) ) {
			printf(
				'<div id="msls-warning" class="%s"><p>%s</p></div>',
				$css_class,
				$message
			);
			return true;
		}
		return false;
	}

	/**
	 * Uninstall plugin
	 * 
	 * The plugin data in all blogs of the current network will be 
	 * deleted after the uninstall procedure. 
	 * @return boolean
	 */
	public static function uninstall() {
		/**
		 * We want to be sure that the user has not deactivated the 
		 * multisite because we need to use switch_to_blog and 
		 * restore_current_blog
		 */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$cache = MslsSqlCacher::init( __CLASS__ )->set_params( __METHOD__ );

			$blogs = $cache->get_results(
				$cache->prepare(
					"SELECT blog_id FROM {$cache->blogs} WHERE blog_id != %d AND site_id = %d",
					$cache->blogid,
					$cache->siteid
				)
			);

			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->blog_id );
				self::cleanup();
				restore_current_blog();
			}
		}
		return self::cleanup();
	}

	/**
	 * Cleanup the options
	 * 
	 * Removes all values of the current blogs which are stored in the 
	 * options-table and returns true if it was successful.
	 * @return boolean
	 */
	public static function cleanup() {
		if ( delete_option( 'msls' ) ) {
			$cache = MslsSqlCacher::init( __CLASS__ )->set_params( __METHOD__ );
			$sql   = $cache->prepare(
				"DELETE FROM {$cache->options} WHERE option_name LIKE %s",
				'msls_%'
			);
			return (bool) $cache->query( $sql );
		}
		return false;
	}

}
