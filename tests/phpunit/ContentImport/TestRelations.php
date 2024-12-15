<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Relations;
use lloc\MslsTests\MslsUnitTestCase;

final class TestRelations extends MslsUnitTestCase {

	public function test_get_data(): void {
		$coordinates = \Mockery::mock( ImportCoordinates::class );
		$test        = new Relations( $coordinates );

		$this->assertIsArray( $test->get_data() );
	}
}
