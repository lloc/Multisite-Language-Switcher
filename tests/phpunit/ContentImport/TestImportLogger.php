<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\Msls\ContentImport\Service;
use lloc\Msls\MslsOptions;
use lloc\MslsTests\MslsUnitTestCase;
use Brain\Monkey\Functions;

class TestImportLogger extends MslsUnitTestCase {

	public function setUp(): void {
		parent::setUp();

		$coordinates = \Mockery::mock( ImportCoordinates::class );

		$this->test = new ImportLogger( $coordinates );
	}

	public function provider_get_data(): array {
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
		$this->assertArrayHasKey( $key, $this->test->get_data() );
	}
}
