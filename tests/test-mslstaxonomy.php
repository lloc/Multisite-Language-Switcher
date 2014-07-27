<?php
/**
 * Tests for MslsTaxonomy
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsTaxonomy
 */
class WP_Test_MslsTaxonomy extends Msls_UnitTestCase {

	/**
	 * Verify the instance-method
	 */
	function test_instance_method() {
		$this->assertInstanceOf( 'MslsTaxonomy', MslsTaxonomy::instance() );
	}

}
