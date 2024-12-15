<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\MslsTests\MslsUnitTestCase;

final class TestImportLogger extends MslsUnitTestCase {

	public static function provider_get_data(): array {
		return array(
			array( 'info' ),
			array( 'error' ),
			array( 'success' ),
		);
	}

	/**
	 * @dataProvider provider_get_data
	 */
	public function test_get_data( $key ): void {
		$coordinates = \Mockery::mock( ImportCoordinates::class );
		$test        = new ImportLogger( $coordinates );

		$this->assertArrayHasKey( $key, $test->get_data() );
	}
}
