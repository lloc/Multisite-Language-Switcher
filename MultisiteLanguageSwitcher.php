<?php

/*
Plugin Name: Multisite Language Switcher
Plugin URI: http://lloc.de/msls
Description: A simple but powerful plugin that will help you to manage the relations of posts/pages/categories/... in your multisite-multilingual-installation.
Version: 0.9
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

if ( !class_exists( 'Msls' ) ) {
    if ( !defined( 'MSLS_PLUGIN_PATH' ) )  
        define( 'MSLS_PLUGIN_PATH', plugin_basename( __FILE__ ) );

    require_once dirname( __FILE__ ) . '/includes/MslsOutput.php';
    register_activation_hook( __FILE__, 'MslsPlugin::activate' );
    register_deactivation_hook( __FILE__, 'MslsPlugin::deactivate' );

    if ( is_admin() ) {
        require_once dirname( __FILE__ ) . '/includes/MslsMetaBox.php';
        add_action( 'load-post.php', 'MslsMetaBox::init' );
        add_action( 'load-post-new.php', 'MslsMetaBox::init' );

        require_once dirname( __FILE__ ) . '/includes/MslsAdmin.php';
        add_action( 'admin_menu', 'MslsAdmin::init' );

        require_once dirname( __FILE__ ) . '/includes/MslsPostTag.php';
        add_action( 'load-edit-tags.php', 'MslsPostTag::init' );

        require_once dirname( __FILE__ ) . '/includes/MslsCustomColumn.php';
        add_action( 'load-edit.php', 'MslsCustomColumn::init' );
        add_action( 'load-edit-tags.php', 'MslsCustomColumnTaxonomy::init' );
    }
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>
