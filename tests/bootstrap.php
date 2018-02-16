<?php

define( 'WP_TESTS_MULTISITE', true );

$_tests_dir = getenv('WP_TESTS_DIR');
if ( ! $_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../MultisiteLanguageSwitcher.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

class Msls_UnitTestCase extends \WP_UnitTestCase {

	/**
	 * SetUp initial settings
	 */
	function setUp() {
		parent::setUp();
		add_filter( 'get_available_languages', array( $this, 'filter_available_languages' ) );
		wp_cache_flush();
	}

	/**
	 * Break down for next test
	 */
	function tearDown() {
		parent::tearDown();
	}

	/**
	 * Filters the list of available languages to allow setting the WPLANG option in blogs.
	 *
	 * @param array $available_languages
	 *
	 * @return array
	 */
	public function filter_available_languages( array $available_languages = array() ) {
		$available_languages[] = 'de_DE';

		return $available_languages;
	}

}
