<?php

namespace lloc\Msls\ContentImport;

class ContentImporterTest extends \Msls_UnitTestCase {

	/**
	 * @var \lloc\Msls\MslsMain
	 */
	public $main;

	/**
	 * @test
	 * it should be instantiatable
	 */
	public function it_should_be_instantiatable() {
		$sut = $this->make_instance();

		$this->assertInstanceOf( ContentImporter::class, $sut );
	}

	/**
	 * @return ContentImporter
	 */
	private function make_instance() {
		return new ContentImporter($this->main->reveal());
	}

	public function test_filter_empty() {

	}

	public function test_on_wp_insert_post() {

	}

	public function test_import_conent(  ) {

	}

}
