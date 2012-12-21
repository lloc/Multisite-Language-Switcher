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
		$options = MslsOptions::instance();
		if ( !$options->is_excluded() )
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
	 * @todo Write the uninstall-method
	 */
	public static function uninstall( $network_wide ) {
		global $wpdb;
		if ( $network_wide ) {
			$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs} WHERE blog_id != {$wpdb->blogid} AND site_id = '{$wpdb->siteid}' AND spam = '0' AND deleted = '0' AND archived = '0'", ARRAY_A ); 
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog['blog_id'] );
				self::cleanup();
				restore_current_blog();
			}
		}
		self::cleanup();
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
			if ( $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'msls_%'" ) !== false )
				return true;
		}
		return false;
	}

}
