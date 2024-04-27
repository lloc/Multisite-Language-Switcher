<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsCustomColumn;
use lloc\Msls\MslsCustomColumnTaxonomy;
use lloc\Msls\MslsOptions;

class TestMslsCustomColumnTaxonomy extends MslsUnitTestCase {

	public function test_th(): void {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( [] )->once();

		$obj = new MslsCustomColumnTaxonomy( $options, $collection );

		$this->assertEmpty( $obj->th( [] ) );
	}

	public function test_column_default(): void {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );

		( new MslsCustomColumnTaxonomy( $options, $collection ) )->column_default( '', 'test', 0 );

		$this->expectOutputString( '' );
	}

}
