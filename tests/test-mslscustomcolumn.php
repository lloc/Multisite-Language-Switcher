<?php
/**
 * Tests for MslsCustomColumn
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsCustomColumn;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;

/**
 * WP_Test_MslsCustomColumn
 */
class WP_Test_MslsCustomColumn extends Msls_UnitTestCase {

	public function get_test() {
		$options    = MslsOptions::instance();
		$collection = MslsBlogCollection::instance();

		return new MslsCustomColumn( $options, $collection );
	}

	function test_th() {
		$obj = $this->get_test();

		$this->assertInternalType( 'array', $obj->th( [] ) );
	}

}
