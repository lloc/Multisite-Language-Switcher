<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsCustomColumn;
use lloc\Msls\MslsCustomColumnTaxonomy;
use lloc\Msls\MslsOptions;

class WP_Test_MslsCustomColumnTaxonomy extends Msls_UnitTestCase {

	function test_th() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		$obj = new MslsCustomColumnTaxonomy( $options, $collection );

		$this->assertEmpty( $obj->th( [] ) );
	}

}
