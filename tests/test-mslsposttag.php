<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsPostTag;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;
use Brain\Monkey\Functions;

/**
 * WP_Test_MslsPostTag
 */
class WP_Test_MslsPostTag extends Msls_UnitTestCase {

	function get_test() {
		Functions\when( 'get_option' )->justReturn( [] );

		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		return new MslsPostTag( $options, $collection );
	}

	/**
	 * Verify the static suggest-method
	 */
	function test_suggest_method() {
		Functions\expect( 'wp_die' );

		$this->assertNull( MslsPostTag::suggest() );
	}

	/**
	 * Verify the static the_input-method
	 */
	function test_the_input_method() {
		$obj = $this->get_test();

		$tag = new \stdClass;
		$tag->term_id = 1;

		$this->assertIsBool( $obj->the_input( $tag, 'test', 'test' ) );
	}

}
