<?php

/**
 * Provides functionalities for activation an deactivation
 *
 * @package Msls
 * @subpackage Main
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
     * 
     * @todo Write the deactivate-method
     */
    public static function deactivate() { }

    /**
     * Uninstall plugin
     * 
     * @todo Write the uninstall-method
     */
    public static function uninstall() { }

}
