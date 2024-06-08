<?php

namespace lloc\MslsTests\ContentImport\LogWriters;

use lloc\Msls\ContentImport\LogWriters\AdminNoticeLogger;
use lloc\MslsTests\MslsUnitTestCase;

class TestAdminNoticeLogger extends MslsUnitTestCase {


	public function setUp(): void {
		parent::setUp();

		$this->test = new AdminNoticeLogger();
	}

	public function test_get_transient(): void {
		$this->assertEquals( 'msls_last_import_log', $this->test->get_transient() );
	}
}
