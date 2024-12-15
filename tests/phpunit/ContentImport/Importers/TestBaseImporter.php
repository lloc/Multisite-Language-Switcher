<?php

namespace lloc\MslsTests\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\BaseImporter;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\Msls\ContentImport\Relations;
use lloc\MslsTests\MslsUnitTestCase;

final class TestBaseImporter extends MslsUnitTestCase {

	private function BaseImporterFactory(): BaseImporter {
		$import_coordinates = \Mockery::mock( ImportCoordinates::class );

		return new BaseImporter( $import_coordinates );
	}

	public function testImport(): void {
		$test = $this->BaseImporterFactory();

		$this->assertEquals( array(), $test->import( array() ) );
	}

	public function testSetImportCoordinates(): void {
		$import_coordinates = \Mockery::mock( ImportCoordinates::class );

		$this->expectNotToPerformAssertions();

		$test = $this->BaseImporterFactory();

		$test->set_import_coordinates( $import_coordinates );
	}

	public function testGetLogger(): void {
		$test = $this->BaseImporterFactory();

		$this->assertInstanceOf( ImportLogger::class, $test->get_logger() );
	}
	public function testGetRelations(): void {
		$test = $this->BaseImporterFactory();

		$this->assertInstanceOf( Relations::class, $test->get_relations() );
	}

	public function testInfo(): void {
		$test = $this->BaseImporterFactory();

		$this->assertInstanceOf( \stdClass::class, $test->info() );
	}
}
