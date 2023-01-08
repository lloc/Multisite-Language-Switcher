<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsMain;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;
use Brain\Monkey\Functions;
use Mockery;

class WP_Test_MslsMain extends Msls_UnitTestCase {

	public function get_sut() {
		Functions\when( 'get_options' )->justReturn( [] );

		$options    = Mockery::mock( MslsOptions::class );

		$blog = Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );

		$collection = Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( $blog );

		return new MslsMain( $options, $collection );
	}

	function test_get_input_array() {
		$obj = $this->get_sut();

		$this->assertIsArray( $obj->get_input_array( 0 ) );
	}

	function test_is_autosave_method() {
		Functions\when( 'wp_is_post_revision' )->justReturn( true );

		$obj = $this->get_sut();

		$this->assertIsBool( $obj->is_autosave( 0 ) );
	}

	function test_verify_nonce_method() {
		$obj = $this->get_sut();

		$this->assertFalse( $obj->verify_nonce() );
	}

}
