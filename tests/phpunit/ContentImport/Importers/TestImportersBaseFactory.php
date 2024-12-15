<?php

namespace lloc\MslsTests\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\ImportersBaseFactory;
use lloc\MslsTests\MslsUnitTestCase;
use Mockery\Mock;

final class TestImportersBaseFactory extends MslsUnitTestCase {

	public function testMake(): void {
		$coordinates = \Mockery::mock( ImportCoordinates::class );
		$coordinates->shouldReceive( 'get_importer_for' )->andReturn( 'post-fields' );

		$test = \Mockery::mock( ImportersBaseFactory::class )->makePartial();

		$this->expectException( \RuntimeException::class );

		$test->make( $coordinates );
	}

	public function testDetails(): void {
		$test = \Mockery::mock( ImportersBaseFactory::class )->makePartial();

		$this->assertNotEmpty( $test->details() );
	}
}
