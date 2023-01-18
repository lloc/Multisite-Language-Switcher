<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsCustomColumn;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;

class WP_Test_MslsCustomColumn extends Msls_UnitTestCase {

	public function get_sut(): MslsCustomColumn {
		$options    = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( [] );

		return new MslsCustomColumn( $options, $collection );
	}

	public function test_th(): void {
		$test = $this->get_sut();

		$this->assertEmpty( $test->th( [] ) );
	}

	public function test_td(): void {
		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'de_DE' );

		$test = $this->get_sut();

		$this->expectOutputString( '' );

		$test->td( 'mslscol', 1 );
	}
}
