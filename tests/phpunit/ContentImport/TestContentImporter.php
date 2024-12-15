<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\ContentImporter;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\Msls\ContentImport\Relations;
use lloc\Msls\MslsMain;
use lloc\MslsTests\MslsUnitTestCase;
use Brain\Monkey\Actions;

final class TestContentImporter extends MslsUnitTestCase {

	private function ContentImporterFactory(): ContentImporter {
		$main = \Mockery::mock( MslsMain::class );
		$main->shouldReceive( 'verify_nonce' )->andReturnTrue();

		return new ContentImporter( $main );
	}

	public function test_logger(): void {
		$test = $this->ContentImporterFactory();

		$test->set_logger( \Mockery::mock( ImportLogger::class ) );

		$this->assertInstanceOf( ImportLogger::class, $test->get_logger() );
	}

	public function test_relations(): void {
		$test = $this->ContentImporterFactory();

		$test->set_relations( \Mockery::mock( Relations::class ) );

		$this->assertInstanceOf( Relations::class, $test->get_relations() );
	}

	public function test_handle_import(): void {
		$test = $this->ContentImporterFactory();

		$this->assertEquals( array(), $test->handle_import() );
	}

	public function test_parse_sources_no_post(): void {
		$test = $this->ContentImporterFactory();

		$this->assertFalse( $test->parse_sources() );
	}

	public function test_handle_false(): void {
		$this->expectNotToPerformAssertions();

		Actions\expectAdded( 'msls_main_save' )->once();

		$this->ContentImporterFactory()->handle( false );
	}

	public function test_handle_true(): void {
		$this->expectNotToPerformAssertions();

		Actions\expectRemoved( 'msls_main_save' )->once();

		$this->ContentImporterFactory()->handle( true );
	}
}
