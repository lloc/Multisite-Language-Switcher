<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsCustomColumn;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;

class WP_Test_MslsCustomColumn extends Msls_UnitTestCase {

	function test_th() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		$obj = new MslsCustomColumn( $options, $collection );

		$this->assertEmpty( $obj->th( [] ) );
	}

	function test_td() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		$obj = new MslsCustomColumn( $options, $collection );

		$this->assertEmpty( $obj->th( [] ) );
	}

}
