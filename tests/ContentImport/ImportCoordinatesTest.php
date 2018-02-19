<?php

namespace lloc\Msls\ContentImport;


class ImportCoordinatesTest extends \Msls_UnitTestCase {

	function setUp() {
		parent::setUp();
		unset(
			$_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ],
			$_POST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ],
			$_GET[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ]
		);
	}

	public function testValidate() {
		$dest_lang   = 'en_US';
		$source_lang = 'de_DE';

		$obj = new ImportCoordinates();

		$this->assertFalse( $obj->validate() );

		$source_blog_id      = $this->factory->blog->create();
		$obj->source_blog_id = $source_blog_id;

		$this->assertFalse( $obj->validate() );

		switch_to_blog( $source_blog_id );
		$source_post    = $this->factory->post->create_and_get();
		$source_post_id = $source_post->ID;

		$obj->source_post_id = $source_post_id;
		$obj->source_post    = $source_post;

		$this->assertFalse( $obj->validate() );

		$dest_blog_id = $this->factory->blog->create();

		$obj->dest_blog_id = $dest_blog_id;

		$this->assertFalse( $obj->validate() );

		update_option( 'WPLANG', $source_lang );

		$obj->source_lang = $source_lang;

		$this->assertFalse( $obj->validate() );

		switch_to_blog( $dest_blog_id );
		$dest_post_id = $this->factory->post->create();

		$obj->dest_post_id = $dest_post_id;

		$this->assertFalse( $obj->validate() );

		update_option( 'WPLANG', $dest_lang );

		$obj->dest_lang = $dest_lang;

		$this->assertTrue( $obj->validate() );
	}

	/**
	 * Test set_importer_for
	 */
	public function test_set_importer_for() {
		$obj = new ImportCoordinates();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );

		$obj->set_importer_for( 'foo', 'bar' );

		$this->assertEquals( 'bar', $obj->get_importer_for( 'foo' ) );

		$this->assertEmpty( $obj->get_importer_for( 'baz' ) );
	}

	/**
	 * Test parse_importers from REQUEST
	 */
	public function test_parse_importers_from_REQUEST() {
		$obj = new ImportCoordinates();

		unset( $_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] );

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );

		$_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [];

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );

		$_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [ 'foo' => 'bar' ];

		$obj->parse_importers();

		$this->assertEquals( 'bar', $obj->get_importer_for( 'foo' ) );
	}

	/**
	 * Test parse_importers from POST
	 */
	public function test_parse_importers_from_POST() {
		$obj = new ImportCoordinates();

		unset( $_POST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] );

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );

		$_POST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [];

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );

		$_POST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [ 'foo' => 'bar' ];

		$obj->parse_importers();

		$this->assertEquals( 'bar', $obj->get_importer_for( 'foo' ) );
	}

	/**
	 * Test parse_importers from GET
	 */
	public function test_parse_importers_from_GET() {
		$obj = new ImportCoordinates();

		unset( $_GET[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] );

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );

		$_GET[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [];

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );

		$_GET[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [ 'foo' => 'bar' ];

		$obj->parse_importers();

		$this->assertEquals( 'bar', $obj->get_importer_for( 'foo' ) );
	}

	/**
	 * Test parse_importers from REQUEST and POST
	 */
	public function test_parse_importers_from_REQUEST_and_POST() {
		$obj = new ImportCoordinates();

		unset( $_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] );
		unset( $_POST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] );

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );

		$_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [];
		$_POST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ]    = [ 'some' => 'baz' ];

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );
		$this->assertEmpty( $obj->get_importer_for( 'some' ) );

		$_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [ 'foo' => 'bar' ];
		$_POST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ]    = [ 'some' => 'baz' ];

		$obj->parse_importers();

		$this->assertEquals( 'bar', $obj->get_importer_for( 'foo' ) );
		$this->assertEmpty( $obj->get_importer_for( 'some' ) );
	}

	/**
	 * Test parse_importers from REQUEST and GET
	 */
	public function test_parse_importers_from_REQUEST_and_GET() {
		$obj = new ImportCoordinates();

		unset( $_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] );
		unset( $_GET[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] );

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );

		$_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [];
		$_GET[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ]     = [ 'some' => 'baz' ];

		$obj->parse_importers();

		$this->assertEmpty( $obj->get_importer_for( 'foo' ) );
		$this->assertEmpty( $obj->get_importer_for( 'some' ) );

		$_REQUEST[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ] = [ 'foo' => 'bar' ];
		$_GET[ ImportCoordinates::IMPORTERS_GLOBAL_KEY ]     = [ 'some' => 'baz' ];

		$obj->parse_importers();

		$this->assertEquals( 'bar', $obj->get_importer_for( 'foo' ) );
		$this->assertEmpty( $obj->get_importer_for( 'some' ) );
	}
}
