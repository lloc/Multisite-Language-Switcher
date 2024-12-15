<?php

namespace lloc\MslsTests\ContentImport\LogWriters;

use Brain\Monkey\Functions;
use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\LogWriters\AdminNoticeLogger;
use lloc\MslsTests\MslsUnitTestCase;

final class TestAdminNoticeLogger extends MslsUnitTestCase {

	public function testGetTransient(): void {
		$test = new AdminNoticeLogger();

		$this->assertEquals( 'msls_last_import_log', $test->get_transient() );
	}

	public function testWrite(): void {
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'set_transient' )->once();

		$coordinates                 = \Mockery::mock( ImportCoordinates::class );
		$coordinates->source_blog_id = 1;
		$coordinates->source_post_id = 42;
		$coordinates->dest_blog_id   = 2;
		$coordinates->dest_post_id   = 13;

		$test = new AdminNoticeLogger();

		$test->set_import_coordinates( $coordinates );

		$data = array( 'info', array( 'foo' ) );
		$test->write( $data );

		$this->expectNotToPerformAssertions();
	}
}
