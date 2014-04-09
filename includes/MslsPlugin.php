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
		if ( ! MslsOptions::instance()->is_excluded() )
			register_widget( 'MslsWidget' );
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

	static function message_handler( $message = null, $css_class = 'error' ) {
		if ( is_null( $message ) ) {
			$message = __( 'This plugin needs the activation of the multisite-feature for working properly. Please read <a onclick="window.open(this.href); return false;" href="http://codex.wordpress.org/Create_A_Network">this post</a> if you don\'t know the meaning.', 'msls' );
		}
		printf(
			'<div id="msls-warning" class="%s"><p>%s</p></div>',
			$css_class,
			$message
		);
	}

	/**
	 * Activate plugin
	 * 
	 * There is a check if the multisite feature is active on the 
	 * activation of the plugin. If it fails the plugin will be
	 * deactivated and the execution will be terminated immediately.
	 */
	static function activate() {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) 
			return;
		deactivate_plugins( MSLS_PLUGIN__FILE__ );
		add_action( 'deactivate_msls', array( 'MslsPlugin', 'message_handler' ) );
		header( 'Location: ' . admin_url( 'plugins.php?deactivate=true' ) );
		exit;
	}

	/**
	 * Deactivate plugin
	 * @todo Write the deactivate-method
	 */
	static function deactivate() { }

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
