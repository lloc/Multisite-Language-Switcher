<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlock;
use lloc\Msls\MslsOptions;

class TestMslsBlock extends MslsUnitTestCase {

	public function test_register_block_excluded_true(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		$this->assertFalse( ( new MslsBlock( $options ) )->register_block() );
	}


	public function test_register_block_excluded_false(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		Functions\expect( 'register_block_type' )->once();
		Functions\expect( 'plugin_dir_path' )->once();

		$this->assertTrue( ( new MslsBlock( $options ) )->register_block() );
	}
}
