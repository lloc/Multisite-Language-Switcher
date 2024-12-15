<?php

namespace lloc\MslsTests\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\Map;
use lloc\MslsTests\MslsUnitTestCase;

final class TestMap extends MslsUnitTestCase {

	public function testMake(): void {
		$coordinates = \Mockery::mock( ImportCoordinates::class );
		$coordinates->shouldReceive( 'get_importer_for' )->andReturn( 'post-fields' );

		$result = ( new Map() )->make( $coordinates );

		$this->assertNotEmpty( $result );
	}
}
