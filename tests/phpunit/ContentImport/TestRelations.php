<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Relations;
use lloc\MslsTests\MslsUnitTestCase;

class TestRelations extends MslsUnitTestCase {

	public function setUp(): void {
		parent::setUp();

		$coordinates = \Mockery::mock( ImportCoordinates::class );

		$this->test = new Relations( $coordinates );
	}

	public function test_get_data(): void {
		$this->assertIsArray( $this->test->get_data() );
	}
}
