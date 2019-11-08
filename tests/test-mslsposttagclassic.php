<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsPostTagClassic;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;

class WP_Test_MslsPostTagClassic extends Msls_UnitTestCase {

	public function get_sut() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		$collection->shouldReceive( 'get' )->once()->andReturn( [] );

		return new MslsPostTagClassic( $options, $collection );
	}

	/**
	 * Verify the static the_input-method
	 */
	public function test_the_input_method() {
		$obj = $this->get_sut();

		$tag = new \StdClass;
		$tag->term_id = 1;

		$this->assertInternalType( 'boolean', $obj->the_input( $tag, 'test', 'test' ) );
	}
}
