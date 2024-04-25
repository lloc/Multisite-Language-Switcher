<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsCustomFilter;
use lloc\Msls\MslsOptions;
use Mockery\Mock;

class TestMslsCustomFilter extends Msls_UnitTestCase {

	function test_execute_filter() {
		$options    = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		$query      = \Mockery::mock( 'WP_Query' );

		$obj        = new MslsCustomFilter( $options, $collection );

		$this->assertFalse( $obj->execute_filter( $query ) );
	}

}
