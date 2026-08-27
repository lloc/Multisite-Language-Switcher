<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestImportLogger extends MslsUnitTestCase {

	/**
	 * @return array<string, array{string}>
	 */
	public static function get_data_provider(): array {
		return array(
			'info'    => array( 'info' ),
			'error'   => array( 'error' ),
			'success' => array( 'success' ),
		);
	}

	#[DataProvider( 'get_data_provider' )]
	public function test_get_data( $key ): void {
		$coordinates = \Mockery::mock( ImportCoordinates::class );
		$test        = new ImportLogger( $coordinates );

		$this->assertArrayHasKey( $key, $test->get_data() );
	}
}
