<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsAdmin;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;

class WP_Test_MslsAdmin extends Msls_UnitTestCase {

	public function get_sut() {
		Functions\when( 'get_option' )->justReturn( [] );
		Functions\when( 'update_option' )->justReturn( true );
		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
		Functions\when( 'checked' )->justReturn( '' );
		Functions\when( 'selected' )->justReturn( '' );
		Functions\when( 'get_admin_url' )->justReturn( 'wp-admin' );
		Functions\when( 'get_locale' )->justReturn( 'de_DE' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_empty' )->andReturns( false );
		$options->shouldReceive( 'get_available_languages' )->andReturns( [ 'de_DE', 'it_IT' ] );

		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_description' )->andReturns( 'ABC' );
		$blog->userblog_id = 1;
		$blog->blogname = 'abc';

		$blogs[] = $blog;

		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_description' )->andReturns( 'XYZ' );
		$blog->userblog_id = 2;
		$blog->blogname = 'xyz';

		$blogs[] = $blog;

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog_id' )->andReturns( 1 );
		$collection->shouldReceive( 'get_plugin_active_blogs' )->andReturns( $blogs );
		$collection->shouldReceive( 'get_users' )->andReturns( [ (object) [ 'ID' => 1, 'user_nicename' => 'realloc' ] ] );

		return new MslsAdmin( $options, $collection );
	}

	function test_has_problems_no_problem() {
		$options    = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_available_languages' )->andReturns( [ 'de_DE', 'it_IT' ] );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$options->shouldReceive( 'is_empty' )->andReturns( false );

		$obj = new MslsAdmin( $options, $collection );

		$this->assertFalse( $obj->has_problems() );
	}

	function test_has_problems_one_language() {
		$options    = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_available_languages' )->andReturns( [ 'de_DE', ] );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$options->shouldReceive( 'is_empty' )->andReturns( false );

		$obj = new MslsAdmin( $options, $collection );

		$this->expectOutputRegex( '/^<div id="msls-warning" class="updated fade"><p>.*$/' );

		$this->assertTrue( $obj->has_problems() );
	}

	function test_has_problems_is_empty() {
		Functions\when( 'get_option' )->justReturn( [] );
		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
		Functions\when( 'admin_url' )->justReturn( '' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_empty' )->andReturns( true );

		$collection = \Mockery::mock( MslsBlogCollection::class );

		$obj = new MslsAdmin( $options, $collection );

		$this->expectOutputRegex( '/^<div id="msls-warning" class="updated fade"><p>.*$/' );

		$this->assertTrue( $obj->has_problems() );
	}

	public function test_subsubsub() {
		$obj = $this->get_sut();

		$this->assertEquals( '<ul class="subsubsub"><li><a href="wp-admin" class="current">abc / ABC</a> | </li><li><a href="wp-admin">xyz / XYZ</a></li></ul>', $obj->subsubsub() );
	}

	/**
	 * Verify the blog_language-method
	 */
	function test_blog_language() {
		$obj = $this->get_sut();

		$this->expectOutputRegex( '/^<select id="blog_language" name="msls\[blog_language\]">.*$/' );
		$obj->blog_language();
	}

	/**
	 * Verify the display-method
	 */
	function test_display() {
		$obj = $this->get_sut();

		$this->expectOutputRegex( '/^<select id="display" name="msls\[display\]">.*$/' );
		$obj->display();
	}

	/**
	 * Verify the reference_user-method
	 */
	function test_reference_user() {
		$obj = $this->get_sut();

		$this->expectOutputRegex( '/^<select id="reference_user" name="msls\[reference_user\]">.*$/' );
		$obj->reference_user();
	}

	/**
	 * Verify the activate_autocomplete-method
	 */
	function test_activate_autocomplete() {
		$obj = $this->get_sut();

		$this->expectOutputString( '<input type="checkbox" id="activate_autocomplete" name="msls[activate_autocomplete]" value="1" /> <label for="activate_autocomplete">Activate experimental autocomplete inputs</label>' );
		$obj->activate_autocomplete();
	}

	/**
	 * Verify the sort_by_description-method
	 */
	function test_sort_by_description() {
		$obj = $this->get_sut();

		$this->expectOutputString( '<input type="checkbox" id="sort_by_description" name="msls[sort_by_description]" value="1" /> <label for="sort_by_description">Sort languages by description</label>' );
		$obj->sort_by_description();
	}

	/**
	 * Verify the exclude_current_blog-method
	 */
	function test_exclude_current_blog() {
		$obj = $this->get_sut();

		$this->expectOutputString( '<input type="checkbox" id="exclude_current_blog" name="msls[exclude_current_blog]" value="1" /> <label for="exclude_current_blog">Exclude this blog from output</label>' );
		$obj->exclude_current_blog();
	}

	/**
	 * Verify the only_with_translation-method
	 */
	function test_only_with_translation() {
		$obj = $this->get_sut();

		$this->expectOutputString( '<input type="checkbox" id="only_with_translation" name="msls[only_with_translation]" value="1" /> <label for="only_with_translation">Show only links with a translation</label>' );
		$obj->only_with_translation();
	}

	/**
	 * Verify the output_current_blog-method
	 */
	function test_output_current_blog() {
		$obj = $this->get_sut();

		$this->expectOutputString( '<input type="checkbox" id="output_current_blog" name="msls[output_current_blog]" value="1" /> <label for="output_current_blog">Display link to the current language</label>' );
		$obj->output_current_blog();
	}

	/**
	 * Verify the description-method
	 */
	function test_description() {
		$obj = $this->get_sut();

		$this->expectOutputString( '<input type="text" class="regular-text" id="description" name="msls[description]" value="" size="40"/>' );
		$obj->description();
	}

	/**
	 * Verify the content_filter-method
	 */
	function test_content_filter() {
		$obj = $this->get_sut();

		$this->expectOutputString( '<input type="checkbox" id="content_filter" name="msls[content_filter]" value="1" /> <label for="content_filter">Add hint for available translations</label>' );
		$obj->content_filter();
	}

	/**
	 * Verify the content_priority-method
	 */
	function test_content_priority() {
		$obj = $this->get_sut();

		$this->expectOutputRegex( '/^<select id="content_priority" name="msls\[content_priority\]">.*$/' );
		$obj->content_priority();
	}

	/**
	 * Verify the validate-method
	 */
	function test_validate() {
		$obj = $this->get_sut();

		$arr = array();
		$this->assertEquals( array( 'display' => 0 ), $obj->validate( $arr ) );
		$arr = array( 'image_url' => '/test/', 'display' => '1' );
		$this->assertEquals( array( 'image_url' => '/test', 'display' => 1 ), $obj->validate( $arr ) );
	}

	/**
	 * Verify the set_blog_language-method
	 */
	function test_set_blog_language() {
		$obj = $this->get_sut();

		$arr = array( 'abc' => true, 'blog_language' => 'it_IT' );
		$this->assertEquals( array( 'abc' => true ), $obj->set_blog_language( $arr ) );
	}

}
