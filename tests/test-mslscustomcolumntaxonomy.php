<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsCustomColumnTaxonomy;
use lloc\Msls\MslsOptions;

class WP_Test_MslsCustomColumnTaxonomy extends Msls_UnitTestCase {

	function test_th() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( [] );

		$obj = new MslsCustomColumnTaxonomy( $options, $collection );

		$this->assertEmpty( $obj->th( [] ) );

		$this->expectOutputString( '' );
		$obj->column_default( '', 'test', 1 );
	}

}
