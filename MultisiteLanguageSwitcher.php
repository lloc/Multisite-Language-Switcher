<?php
/**
 * Multisite Language Switcher Plugin
 *
 * Plugin Name: Multisite Language Switcher
 * Version: 3.0.1
 * Plugin URI: http://msls.co/
 * Description: A simple but powerful plugin that will help you to manage the relations of your contents in a multilingual multisite-installation.
 * Author: Dennis Ploetner
 * Author URI: http://lloc.de/
 * Text Domain: multisite-language-switcher
 * Domain Path: /languages/
 * License: GPLv2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @copyright Copyright (C) 2011-2022, Dennis Ploetner, re@lloc.de
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 or later
 * @wordpress-plugin
 * @package msls
 */

declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MultisiteLanguageSwitcher
 *
 * @author Dennis Ploetner <re@lloc.de>
 */
if ( ! defined( 'MSLS_PLUGIN_VERSION' ) ) {
	define( 'MSLS_PLUGIN_VERSION', '3.0.1' );
	define( 'MSLS_PLUGIN_PATH', plugin_basename( __FILE__ ) );
	define( 'MSLS_PLUGIN__FILE__', __FILE__ );

	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require __DIR__ . '/vendor/autoload.php';
	}

	/**
	 * Loaded here and not on plugins_loaded: add-ons are free to run before us, and the
	 * backwards-compatibility aliases and the msls_*() functions have to be in place from
	 * the moment this file is included.
	 */
	require_once __DIR__ . '/includes/aliases.php';
	require_once __DIR__ . '/includes/deprecated.php';
	require_once __DIR__ . '/includes/api.php';

	add_action(
		'plugins_loaded',
		function () {
			lloc\Msls\Plugin::init();
			lloc\Msls\Cli\Cli::init();
		}
	);
}
