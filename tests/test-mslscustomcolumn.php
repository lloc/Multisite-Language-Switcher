<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsCustomColumn;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;
use Brain\Monkey\Functions;

class WP_Test_MslsCustomColumn extends Msls_UnitTestCase {

	function test_th() {
		Functions\expect( 'add_query_arg' )->twice()->andReturn( 'https://example.org/added-args' );
		Functions\expect( 'get_the_ID' )->twice()->andReturnValues( [ 1, 2 ] );
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 1 ) . '/' );

		$options = \Mockery::mock( MslsOptions::class );
		$obj     = new MslsCustomColumn( $options, $this->getBlogsCollection() );

		$expected = [ 'mslscol' => '<span class="flag-icon flag-icon-de">de_DE</span>&nbsp;<span class="flag-icon flag-icon-us">en_US</span>' ];
		$this->assertEquals( $expected, $obj->th( [] ) );
	}

	function test_th_empty() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		$obj = new MslsCustomColumn( $options, $collection );

		$this->assertEmpty( $obj->th( [] ) );
	}

}
