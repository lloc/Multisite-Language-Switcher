<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\ContentImporter;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\Msls\ContentImport\Relations;
use lloc\Msls\MslsMain;
use lloc\MslsTests\MslsUnitTestCase;
use Brain\Monkey\Actions;

class TestContentImporter extends MslsUnitTestCase {


	public function setUp(): void {
		parent::setUp();

		$main = \Mockery::mock( MslsMain::class );
		$main->shouldReceive( 'verify_nonce' )->andReturnTrue();

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

	public function test_handle_import() {
		$this->assertEquals( array(), $this->test->handle_import() );
	}

	public function test_parse_sources_no_post() {
		$this->assertFalse( $this->test->parse_sources() );
	}

	public function test_handle_false() {
		$this->expectNotToPerformAssertions();

		Actions\expectAdded( 'msls_main_save' )->once();

		$this->test->handle( false );
	}

	public function test_handle_true() {
		$this->expectNotToPerformAssertions();

		Actions\expectRemoved( 'msls_main_save' )->once();

		$this->test->handle( true );
	}
}
