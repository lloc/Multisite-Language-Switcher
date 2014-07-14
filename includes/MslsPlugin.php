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
	 */
	static function init_widget() {
		if ( ! MslsOptions::instance()->is_excluded() ) {
			register_widget( 'MslsWidget' );
		}
	}

	/**
	 * Load textdomain
	 * 
	 * The method will be executed allways on init because we have some
	 * translatable string in the frontend too.
	 */
	static function init_i18n_support() {
		load_plugin_textdomain(
			'msls',
			false,
			dirname( MSLS_PLUGIN_PATH ) . '/languages/'
		);
	}

	/**
	 * Message handler
	 * 
	 * Prints a message box to the screen.
	 * @param string $message
	 * @param string $css_class
	 */
	static function message_handler( $message, $css_class = 'error' ) {
		printf(
			'<div id="msls-warning" class="%s"><p>%s</p></div>',
			$css_class,
			$message
		);
	}

	/**
	 * Uninstall plugin
	 * 
	 * The plugin data in all blogs of the current network will be 
	 * deleted after the uninstall procedure. 
	 * @return bool
	 */
	static function uninstall() {
		/**
		 * I want to be sure that the user has not deactivated the 
		 * multisite because I'd like to use switch_to_blog and 
		 * restore_current_blog
		 */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			global $wpdb;

			$cache = new MslsSqlCacher( $wpdb, __CLASS__, __METHOD__ );

			$sql = $wpdb->prepare(
				"SELECT blog_id FROM {$wpdb->blogs} WHERE blog_id != %d AND site_id = %d",
				$wpdb->blogid,
				$wpdb->siteid
			);

			foreach ( $cache->get_results( $sql, ARRAY_A ) as $blog ) {
				switch_to_blog( $blog['blog_id'] );
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
	 * @return bool
	 */
	static function cleanup() {
		if ( delete_option( 'msls' ) ) {
			global $wpdb;
			$sql = $wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				'msls_%'
			);
			return (bool) $wpdb->query( $sql );
		}
		return false;
	}

}
