<?php

namespace lloc\Msls\ContentImport;


class ImportCoordinatesTest extends \Msls_UnitTestCase {

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
}
