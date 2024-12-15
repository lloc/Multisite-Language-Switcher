<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\Service;
use lloc\Msls\MslsOptions;
use lloc\MslsTests\MslsUnitTestCase;
use Brain\Monkey\Functions;

final class TestService extends MslsUnitTestCase {

	public function test_register_not_active_false(): void {
		$options = \Mockery::mock( MslsOptions::class );

		$options->activate_content_import = false;

		Functions\expect( 'msls_options' )->once()->andReturn( $options );

		$this->assertFalse( ( new Service() )->register() );
	}

	public function test_register_active_true(): void {
		$options = \Mockery::mock( MslsOptions::class );

		$options->activate_content_import = true;

		Functions\expect( 'msls_options' )->once()->andReturn( $options );

		$this->assertTrue( ( new Service() )->register() );
	}
}
