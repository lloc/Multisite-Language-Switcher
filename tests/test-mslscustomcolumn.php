<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsCustomColumn;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;

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
