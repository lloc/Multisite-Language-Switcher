<?php

namespace lloc\MslsTests\ContentImport;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\MslsTests\MslsUnitTestCase;
use Brain\Monkey\Functions;

class TestImportCoordinates extends MslsUnitTestCase {


	public function setUp(): void {
		parent::setUp();

		$this->test = new ImportCoordinates();

		$this->test->source_blog_id = 1;
		$this->test->source_post_id = 42;
		$this->test->dest_blog_id   = 2;
		$this->test->dest_post_id   = 13;
		$this->test->source_post    = \Mockery::mock( \WP_Post::class );
		$this->test->source_lang    = 'de_DE';
		$this->test->dest_lang      = 'it_IT';
	}

	public static function providerValidate(): array {
		$post = \Mockery::mock( \WP_Post::class );
		return array(
			array( null, null, null, null, null, false ),
			array( $post, null, null, null, null, false ),
			array( $post, $post, null, null, null, false ),
			array( $post, $post, $post, null, null, false ),
			array( $post, $post, $post, 'de_DE', null, false ),
			array( $post, $post, $post, 'de_DE', 'it_IT', true ),
		);
	}

	/**
	 * @dataProvider providerValidate
	 */
	public function testValidate( $post_a, $post_b, $source_post, $lang_a, $lang_b, $expected ): void {
		Functions\expect( 'get_blog_post' )->andReturn( $post_a, $post_b );
		Functions\expect( 'get_blog_option' )->andReturn( $lang_a, $lang_b );

		$this->test->source_post = $source_post;

		$this->assertEquals( $expected, $this->test->validate() );
	}

	public function testParseImportersFromPost(): void {
		Functions\expect( 'filter_has_var' )
			->once()
			->with( INPUT_POST, ImportCoordinates::IMPORTERS_GLOBAL_KEY )
			->andReturn( false );
		Functions\expect( 'filter_has_var' )
			->once()
			->with( INPUT_GET, ImportCoordinates::IMPORTERS_GLOBAL_KEY )
			->andReturn( true );
		Functions\expect( 'filter_input' )
			->once()
			->with( INPUT_GET, ImportCoordinates::IMPORTERS_GLOBAL_KEY, FILTER_FORCE_ARRAY )
			->andReturn( array( 'pagesType' => 'pagesSlug' ) );

		$this->assertNull( $this->test->get_importer_for( 'pagesType' ) );

		$this->test->parse_importers_from_request();

		$this->assertEquals( 'pagesSlug', $this->test->get_importer_for( 'pagesType' ) );
	}

	public function testSetImporterFor(): void {
		$this->test->set_importer_for( 'postsType', 'postsSlug' );

		$this->assertEquals( 'postsSlug', $this->test->get_importer_for( 'postsType' ) );
	}
}
