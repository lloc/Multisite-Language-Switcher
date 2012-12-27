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
	 * Registers widget
	 */
	function init_widget() {
		if ( !MslsOptions::instance()->is_excluded() )
			register_widget( 'MslsWidget' );
	}

	/**
	 * Load textdomain
	 */
	public static function init_i18n_support() {
		load_plugin_textdomain(
			'msls',
			false,
			dirname( MSLS_PLUGIN_PATH ) . '/languages/'
		);
	}

	/**
	 * Activate plugin
	 */
	public static function activate() {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) 
			return; 
		deactivate_plugins( __FILE__ );
		die(
			"This plugin needs the activation of the multisite-feature for working properly. Please read <a onclick='window.open(this.href); return false;' href='http://codex.wordpress.org/Create_A_Network'>this post</a> if you don't know the meaning.\n"
		);
	}

	/**
	 * Deactivate plugin
	 * @todo Write the deactivate-method
	 */
	public static function deactivate() { }

	/**
	 * Uninstall plugin
	 * @return bool
	 */
	public static function uninstall() {
		/**
		 * I want to be sure that the user has not deactivated the 
		 * multisite because I'd like to use switch_to_blog and 
		 * restore_current_blog
		 */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			global $wpdb;
			$sql = $wpdb->prepare(
				"SELECT blog_id FROM {$wpdb->blogs} WHERE blog_id != %d AND site_id = %d",
				$wpdb->blogid,
				$wpdb->siteid
			);
			foreach ( $wpdb->get_results( $sql, ARRAY_A ) as $blog ) {
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
	 * Cleanup (remove) all values of the current blogs which are stored
	 * in the options-table and return the boolean true if it was 
	 * successful.
	 * @return bool
	 */
	public static function cleanup() {
		if ( delete_option( 'msls' ) ) {
			global $wpdb;
			return (bool) $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'msls_%'" );
		}
		return false;
	}

}
