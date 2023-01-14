<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsCustomFilter;
use lloc\Msls\MslsOptions;
use function Patchwork\always;
use function Patchwork\redefine;

class WP_Test_MslsCustomFilter extends Msls_UnitTestCase {

	public function setUp(): void {
		parent::setUp();

		redefine( 'filter_has_var', always( true ) );
		redefine( 'filter_input', always( 1 ) );

	}

	public function get_sut( $blogs = [] ): MslsCustomFilter {
		$options    = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( $blogs );

		return new MslsCustomFilter( $options, $collection );
	}

	public function test_add_filter_no_blogs() {
		$sut = $this->get_sut();

		$this->expectOutputString( '' );

		$sut->add_filter();
	}

	public function test_add_filter_with_blogs() {
		$sut = $this->get_sut();

		$this->expectOutputString( '' );

		$sut->add_filter();
	}

	function test_execute_filter() {
		$sut = $this->get_sut();

		$query = \Mockery::mock( 'WP_Query' );

		$this->assertFalse( $sut->execute_filter( $query ) );
	}

}
