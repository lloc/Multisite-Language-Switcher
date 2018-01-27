<?php

define( 'WP_TESTS_MULTISITE', true );

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../MultisiteLanguageSwitcher.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

class Msls_UnitTestCase extends WP_UnitTestCase {

	/**
	 * SetUp initial settings
	 */
	function setUp() {
		parent::setUp();
		wp_cache_flush();
	}

	/**
	 * Break down for next test
	 */
	function tearDown() {
		parent::tearDown();
	}

	/**
	 * Polyfill to make sure whatever mocking method is supported will be used.
	 *
	 * The `getMock` method has been removed on latest versions of PHPUnit.
	 *
	 * @param string $class The class to mock
	 *
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	public function getMock( $class ) {
		return $this->getMockBuilder( $class )->getMock();
	}
}
