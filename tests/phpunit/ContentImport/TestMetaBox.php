<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\MetaBox;
use lloc\MslsTests\MslsUnitTestCase;

class TestMetaBox extends MslsUnitTestCase {


	public function setUp(): void {
		parent::setUp();

		$this->test = new MetaBox();
	}

	public function test_print_modal_html(): void {
		$this->assertEquals( '', $this->test->print_modal_html() );
	}
}
