<?php

/*
Plugin Name: Multisite Language Switcher
Plugin URI: http://msls.co/
Description: A simple but powerful plugin that will help you to manage the relations of your contents in a multilingual multisite-installation.
Version: 2.4.6
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

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

/**
 * MultisiteLanguageSwitcher
 *
 * @author Dennis Ploetner <re@lloc.de>
 */
if ( ! defined( 'MSLS_PLUGIN_VERSION' ) ) {
	define( 'MSLS_PLUGIN_VERSION', '2.4.6' );
	define( 'MSLS_PLUGIN_PATH', plugin_basename( __FILE__ ) );
	define( 'MSLS_PLUGIN__FILE__', __FILE__ );

	lloc\Msls\MslsPlugin::init();

	/**
	 * Get the output for using the links to the translations in your code
	 *
	 * @package Msls
	 *
	 * @param array $attr
	 *
	 * @return string
	 */
	function get_the_msls( $attr ) {
		$arr = is_array( $attr ) ? $attr : [];
		$obj = apply_filters( 'msls_get_output', null );

		return ! is_null( $obj ) ? strval( $obj->set_tags( $arr ) ) : '';
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
	 *
	 * @param array $arr
	 */
	function the_msls( array $arr = [] ) {
		echo get_the_msls( $arr );
	}

	/**
	 * Gets the URL of the country flag-icon for a specific locale
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	function get_msls_flag_url( $locale ) {
		return ( new \lloc\Msls\MslsOptions )->get_flag_url( $locale );
	}

	/**
	 * Gets the description for a blog for a specific locale
	 *
	 * @param string $locale
	 *
	 * @return bool|string
	 */
	function get_msls_blog_description( $locale ) {
		$blog = \lloc\Msls\MslsBlogCollection::instance()->get_blog( $locale );

		return $blog->get_description();
	}

	/**
	 * Gets the permalink for a translation of the current post in a given language
	 *
	 * @param string $locale
	 *
	 * @return string
	 */
	function get_msls_permalink( $locale ) {
		$options = \lloc\Msls\MslsOptions::create();
		$blog    = \lloc\Msls\MslsBlogCollection::instance()->get_blog( $locale );

		return $blog->get_url( $options );
	}

}
