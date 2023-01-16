<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsCustomFilter;
use lloc\Msls\MslsOptions;
use function Patchwork\always;
use function Patchwork\redefine;
use function Patchwork\restoreAll;

class WP_Test_MslsCustomFilter extends Msls_UnitTestCase {

	public function setUp(): void {
		parent::setUp();

		redefine( 'filter_input', always( 1 ) );
	}

	public function tearDown(): void {
		parent::tearDown();

		restoreAll();
	}

	public function get_sut( $blogs = [] ): MslsCustomFilter {
		$options    = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( $blogs );

		return new MslsCustomFilter( $options, $collection );
	}

	public function test_add_filter_has_var_false() {
		redefine( 'filter_has_var', always( false ) );

		$sut = $this->get_sut();

		$this->expectOutputString( '' );

		$sut->add_filter();
	}

	public function test_add_filter() {
		redefine( 'filter_has_var', always( true ) );

		$sut = $this->get_sut();

		$this->expectOutputString( '' );

		$sut->add_filter();
	}

	function test_execute_filter_has_var_false() {
		redefine( 'filter_has_var', always( false ) );

		$sut = $this->get_sut();

		$query = \Mockery::mock( 'WP_Query' );

		$this->assertFalse( $sut->execute_filter( $query ) );
	}

	function test_execute_filter() {
		redefine( 'filter_has_var', always( true ) );

		$sut = $this->get_sut();

		$query = \Mockery::mock( 'WP_Query' );

		$this->assertFalse( $sut->execute_filter( $query ) );
	}

}
