<?php

/*
Plugin Name: Multisite Language Switcher
Plugin URI: http://lloc.de/msls
Description: A simple but powerful plugin that will help you to manage the relations of your contents in a multilingual multisite-installation.
Version: 0.9.6
Author: Dennis Ploetner 
Author URI: http://lloc.de/
*/

/*
Copyright 2011  Dennis Ploetner  (email : re@lloc.de)

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

if ( !class_exists( 'MslsAutoloader' ) ) {
    if ( !defined( 'MSLS_PLUGIN_PATH' ) )
        define( 'MSLS_PLUGIN_PATH', plugin_basename( __FILE__ ) );
	if ( !defined( 'MSLS_PLUGIN__FILE__' ) )
    	define( 'MSLS_PLUGIN__FILE__', __FILE__ );

    /**
     * The Autoloader does all the magic when it comes to include a file
     * @package Msls
     */
    class MslsAutoloader {

        /**
         * Static loader method
         * @param $class_name
         */
        public static function load( $class_name ) {
            require_once dirname( __FILE__ ) . '/includes/' . $class_name . '.php';
        }
    }
    spl_autoload_register( array( 'MslsAutoloader', 'load' ) );

    register_activation_hook( __FILE__, array( 'MslsPlugin', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'MslsPlugin', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'MslsPlugin', 'uninstall' ) );
    add_action( 'init', array( 'MslsPlugin', 'init_i18n_support' ) );

    if ( is_admin() ) {
        add_action( 'admin_menu', array( 'MslsAdmin', 'init' ) );

        add_action( 'load-post.php', array( 'MslsMetaBox', 'init' ) );
        add_action( 'load-post-new.php', array( 'MslsMetaBox', 'init' ) );

        add_action( 'load-edit-tags.php', array( 'MslsPostTag', 'init' ) );

        add_action( 'load-edit.php', array( 'MslsCustomColumn', 'init' ) );
        add_action( 'load-edit-tags.php', array( 'MslsCustomColumnTaxonomy', 'init' ) );

        if ( isset( $_POST['action'] ) && $_POST['action'] == 'add-tag' ) {
            add_action( 'admin_init', array( 'MslsPostTag', 'init' ) );
            add_action( 'admin_init', array( 'MslsCustomColumnTaxonomy', 'init' ) );
        }
    }
}

?>
