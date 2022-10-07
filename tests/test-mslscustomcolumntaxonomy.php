<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsCustomColumnTaxonomy;
use lloc\Msls\MslsOptions;
use Brain\Monkey\Functions;

class WP_Test_MslsCustomColumnTaxonomy extends Msls_UnitTestCase {

	function test_th() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		$obj = new MslsCustomColumnTaxonomy( $options, $collection );

		$this->assertEmpty( $obj->th( [] ) );
	}

	function test_td_empty_output() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		$obj = new MslsCustomColumnTaxonomy( $options, $collection );

		$this->expectOutputString( '' );
		$obj->column_default( '', 'test', 1 );
	}

	function test_td() {
		Functions\expect( 'get_current_blog_id' )->once()->andReturns( 1 );
		Functions\expect( 'get_blog_option' )->once()->andReturns( 'it_IT' );

		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		$obj = new MslsCustomColumnTaxonomy( $options, $collection );

		$this->expectOutputString( '' );
		$obj->column_default( '', 'mslscol', 1 );
	}

}
