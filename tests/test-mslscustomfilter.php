<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsCustomFilter;

class WP_Test_MslsCustomFilter extends Msls_UnitTestCase {

	function test_init_method() {
		$obj   = MslsCustomFilter::init();
		$query = $this->getMockBuilder( WP_Query::class )->getMock();

		$this->assertInstanceOf( MslsCustomFilter::class, $obj );
		$this->assertFalse( $obj->execute_filter( $query ) );
	}

}
