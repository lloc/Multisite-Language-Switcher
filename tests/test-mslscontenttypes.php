<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsContentTypes;
use lloc\Msls\MslsPostType;

class WP_Test_MslsContentTypes extends Msls_UnitTestCase {

	public function test_create() {
		Functions\expect('get_post_types' )->twice()->andReturn( [] );

		$obj = MslsContentTypes::create();

		$this->assertInstanceOf( MslsPostType::class, $obj );
	}

}
