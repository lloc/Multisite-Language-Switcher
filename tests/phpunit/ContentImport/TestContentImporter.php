<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\ContentImporter;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\Msls\ContentImport\Relations;
use lloc\Msls\MslsMain;
use lloc\MslsTests\MslsUnitTestCase;

class TestContentImporter extends MslsUnitTestCase {


	public function setUp(): void {
		parent::setUp();

		$main = \Mockery::mock( MslsMain::class );

		$this->test = new ContentImporter( $main );
	}

	public function test_logger(): void {
		$this->test->set_logger( \Mockery::mock( ImportLogger::class ) );

		$this->assertInstanceOf( ImportLogger::class, $this->test->get_logger() );
	}

	public function test_relations(): void {
		$this->test->set_relations( \Mockery::mock( Relations::class ) );

		$this->assertInstanceOf( Relations::class, $this->test->get_relations() );
	}
}
