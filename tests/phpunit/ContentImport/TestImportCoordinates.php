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

	public static function provider_validate(): array {
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
	 * @dataProvider provider_validate
	 */
	public function test_validate( $post_a, $post_b, $source_post, $lang_a, $lang_b, $expected ): void {
		Functions\expect( 'get_blog_post' )->andReturn( $post_a, $post_b );
		Functions\expect( 'get_blog_option' )->andReturn( $lang_a, $lang_b );

		$this->test->source_post = $source_post;

		$this->assertEquals( $expected, $this->test->validate() );
	}
}
