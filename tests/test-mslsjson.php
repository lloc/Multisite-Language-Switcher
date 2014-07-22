<?php
/**
 * Tests for MslsJson
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsJson
 */
class WP_Test_MslsJson extends WP_UnitTestCase {

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
	 * Verify the add-, the get- and the compare-methods
	 */
	function test_add_get_methods() {
		$obj = new MslsJson();
		$obj->add( null, 'Test 3' )
			->add( '2', 'Test 2' )
			->add( 1, 'Test 1' );
		$this->assertEquals( 
			array( 
				array( 'value' => 1, 'label' => 'Test 1' ),
				array( 'value' => 2, 'label' => 'Test 2' ),
				array( 'value' => 0, 'label' => 'Test 3' ),
			),
			$obj->get()
		);
		return $obj;
	}

	/**
	 * Verify the get- and the __toString-methods
	 * @depends test_add_get_methods
	 */
	function test_get_toString_methods( $obj ) {
		$this->assertEquals(
			'[{"value":1,"label":"Test 1"},{"value":2,"label":"Test 2"},{"value":0,"label":"Test 3"}]',
			$obj->__toString()
		);
	}

}
