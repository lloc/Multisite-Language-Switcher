<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsTaxTerm;
use Mockery\Mock;

class WP_Test_MslsOptionsTaxTerm extends Msls_UnitTestCase {

	public function test_object() {
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		$obj = new MslsOptionsTaxTerm( 0 );

		$this->assertIsSTring( $obj->get_postlink( '' ) );
	}

	public function test_check_base() {
		Functions\expect( 'get_option' )->once()->andReturn( '' );

		$options = \Mockery::mock( MslsOptionsTaxTerm::class );
		$options->shouldReceive( 'get_tax_query' )->once()->andReturn( 'test' );

		$this->assertEquals( 'https://example.org', MslsOptionsTaxTerm::check_base( 'https://example.org', $options ) );
	}

	public function test_check_base_empty_url() {
		$options = \Mockery::mock( MslsOptionsTaxTerm::class );

		$this->assertEquals( '', MslsOptionsTaxTerm::check_base( '', $options ) );
	}

	public function test_get_base_defined() {
		$this->assertEquals( 'tag', MslsOptionsTaxTerm::get_base_defined( 'test', 'tag' ) );
	}

	public function test_get_base_option_empty_option() {
		Functions\expect( 'get_option' )->once()->andReturn( '' );

		$this->assertEquals( 'tag', MslsOptionsTaxTerm::get_base_option( 'test', 'tag' ) );
	}

	public function test_get_base_option_with_option() {
		Functions\expect( 'get_option' )->once()->andReturn( 'test' );

		$this->assertEquals( 'test', MslsOptionsTaxTerm::get_base_option( 'test', 'tag' ) );
	}

}
