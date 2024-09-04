<?php

namespace lloc\MslsTests\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\BaseImporter;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\Msls\ContentImport\Relations;
use lloc\MslsTests\MslsUnitTestCase;

class TestBaseImporter extends MslsUnitTestCase {

	public function setUp(): void {
		parent::setUp();

		$import_coordinates = \Mockery::mock( ImportCoordinates::class );
		$this->test         = new BaseImporter( $import_coordinates );
	}

	public function testImport(): void {
		$this->assertEquals( array(), $this->test->import( array() ) );
	}

	public function testSetImportCoordinates(): void {
		$import_coordinates = \Mockery::mock( ImportCoordinates::class );

		$this->expectNotToPerformAssertions();
		$this->test->set_import_coordinates( $import_coordinates );
	}

	public function testGetLogger() {
		$this->assertInstanceOf( ImportLogger::class, $this->test->get_logger() );
	}
	public function testGetRelations() {
		$this->assertInstanceOf( Relations::class, $this->test->get_relations() );
	}

	public function testInfo() {
		$this->assertInstanceOf( \stdClass::class, $this->test->info() );
	}
}
