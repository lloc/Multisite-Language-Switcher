<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsCustomColumnTaxonomy;

class WP_Test_MslsCustomColumnTaxonomy extends Msls_UnitTestCase {

	/**
	 * Verify the init-method
	 */
	function test_init_method() {
		$this->assertInstanceOf( MslsCustomColumnTaxonomy::class, MslsCustomColumnTaxonomy::init() );
	}

}
