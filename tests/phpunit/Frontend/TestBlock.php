<?php declare( strict_types=1 );

namespace lloc\MslsTests\Frontend;

use Brain\Monkey\Functions;
use lloc\Msls\Frontend\Block;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;

final class TestBlock extends MslsUnitTestCase {

	public function test_register_block_excluded_true(): void {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		$this->assertFalse( ( new Block( $options ) )->register_block() );
	}


	public function test_register_block_excluded_false(): void {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		Functions\expect( 'register_block_type' )->once();
		Functions\expect( 'plugin_dir_path' )->once();

		$this->assertTrue( ( new Block( $options ) )->register_block() );
	}
}
