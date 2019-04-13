<?php
/**
 * Tests for MslsAdmin
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsAdmin;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsBlog;

/**
 * WP_Test_MslsAdmin
 */
class WP_Test_MslsAdmin extends Msls_UnitTestCase {

	function get_test() {
		$options    = MslsOptions::instance();
		$collection = MslsBlogCollection::instance();

		return new MslsAdmin( $options, $collection );
	}

	function test_has_problems_no_problem() {
		$options = $this->getMockBuilder( MslsOptions::class )->getMock();
		$options->method( 'get_available_languages' )->willReturn( [ 'de_DE', 'it_IT' ] );

		$collection = MslsBlogCollection::instance();

		$obj = new MslsAdmin( $options, $collection );

		$this->assertFalse( $obj->has_problems() );
	}

	function test_has_problems_one_language() {
		$options = $this->getMockBuilder( MslsOptions::class )->getMock();
		$options->method( 'get_available_languages' )->willReturn( [ 'de_DE' ] );

		$collection = MslsBlogCollection::instance();

		$obj = new MslsAdmin( $options, $collection );

		$this->expectOutputRegex( '/^<div id="msls-warning" class="updated fade"><p>.*$/' );

		$this->assertTrue( $obj->has_problems() );
	}

	function test_has_problems_is_empty() {
		$options = $this->getMockBuilder( MslsOptions::class )->getMock();
		$options->method( 'is_empty' )->willReturn( true );

		$collection = MslsBlogCollection::instance();

		$obj = new MslsAdmin( $options, $collection );

		$this->expectOutputRegex( '/^<div id="msls-warning" class="updated fade"><p>.*$/' );

		$this->assertTrue( $obj->has_problems() );
	}

	public function test_subsubsub_empty() {
		$obj = $this->get_test();

		$this->assertEquals( '', $obj->subsubsub() );
	}

	/* function test_subsubsub_two_blogs() {
		$options    = $this->getMockBuilder( MslsOptions::class )->getMock();

		$blog_1              = $this->getMockBuilder( MslsBlog::class)->disableOriginalConstructor()->getMock();
		$blog_1->userblog_id = 1;
		$blog_1->blogname    = 'Test 1';
		$blog_1->method( 'get_description' )->willReturn( 'Descr 1' );

		$blog_2              = $this->getMockBuilder( MslsBlog::class)->disableOriginalConstructor()->getMock();
		$blog_2->userblog_id = 2;
		$blog_2->blogname    = 'Test 2';
		$blog_2->method( 'get_description' )->willReturn( 'Descr 2' );

		$collection = $this->getMockBuilder( MslsBlogCollection::class)->getMock();
		$collection->method( 'get_plugin_active_blogs' )->willReturn( [ $blog_1, $blog_2 ] );
		$collection->method( 'get_current_blog_id' )->willReturn( 1 );

		$obj = new MslsAdmin( $options, $collection );

		$this->assertInternalType( 'string', $obj->subsubsub() );
	} */

	/**
	 * Verify the blog_language-method
	 */
	function test_blog_language() {
		$obj = $this->get_test();

		$this->expectOutputRegex( '/^<select id="blog_language" name="msls\[blog_language\]">.*$/' );
		$obj->blog_language();
	}

	/**
	 * Verify the display-method
	 */
	function test_display() {
		$obj = $this->get_test();

		$this->expectOutputRegex( '/^<select id="display" name="msls\[display\]">.*$/' );
		$obj->display();
	}

	/**
	 * Verify the reference_user-method
	 */
	function test_reference_user() {
		$obj = $this->get_test();

		$this->expectOutputRegex( '/^<select id="reference_user" name="msls\[reference_user\]">.*$/' );
		$obj->reference_user();
	}

	/**
	 * Verify the activate_autocomplete-method
	 */
	function test_activate_autocomplete() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="activate_autocomplete" name="msls[activate_autocomplete]" value="1" /> <label for="activate_autocomplete">Activate experimental autocomplete inputs</label>' );
		$obj->activate_autocomplete();
	}

	/**
	 * Verify the sort_by_description-method
	 */
	function test_sort_by_description() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="sort_by_description" name="msls[sort_by_description]" value="1" /> <label for="sort_by_description">Sort languages by description</label>' );
		$obj->sort_by_description();
	}

	/**
	 * Verify the exclude_current_blog-method
	 */
	function test_exclude_current_blog() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="exclude_current_blog" name="msls[exclude_current_blog]" value="1" /> <label for="exclude_current_blog">Exclude this blog from output</label>' );
		$obj->exclude_current_blog();
	}

	/**
	 * Verify the only_with_translation-method
	 */
	function test_only_with_translation() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="only_with_translation" name="msls[only_with_translation]" value="1" /> <label for="only_with_translation">Show only links with a translation</label>' );
		$obj->only_with_translation();
	}

	/**
	 * Verify the output_current_blog-method
	 */
	function test_output_current_blog() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="output_current_blog" name="msls[output_current_blog]" value="1" /> <label for="output_current_blog">Display link to the current language</label>' );
		$obj->output_current_blog();
	}

	/**
	 * Verify the description-method
	 */
	function test_description() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="text" class="regular-text" id="description" name="msls[description]" value="" size="40"/>' );
		$obj->description();
	}

	/**
	 * Verify the content_filter-method
	 */
	function test_content_filter() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="content_filter" name="msls[content_filter]" value="1" /> <label for="content_filter">Add hint for available translations</label>' );
		$obj->content_filter();
	}

	/**
	 * Verify the content_priority-method
	 */
	function test_content_priority() {
		$obj = $this->get_test();

		$this->expectOutputRegex( '/^<select id="content_priority" name="msls\[content_priority\]">.*$/' );
		$obj->content_priority();
	}

	/**
	 * Verify the render_checkbox-method
	 */
	function test_render_checkbox() {
		$obj = $this->get_test();

		$this->assertInternalType( 'string', $obj->render_checkbox( 'test' ) );
	}

	/**
	 * Verify the render_input-method
	 */
	function test_render_input() {
		$obj = $this->get_test();

		$this->assertInternalType( 'string', $obj->render_input( 'test' ) );
	}

	/**
	 * Verify the render_select-method
	 */
	function test_render_select() {
		$obj = $this->get_test();

		$arr = array( 'a', 'b', 'c' );
		$this->assertInternalType( 'string', $obj->render_select( 'test', $arr ) );
	}

	/**
	 * Verify the validate-method
	 */
	function test_validate() {
		$obj = $this->get_test();

		$arr = array();
		$this->assertEquals( array( 'display' => 0 ), $obj->validate( $arr ) );
		$arr = array( 'image_url' => '/test/', 'display' => '1' );
		$this->assertEquals( array( 'image_url' => '/test', 'display' => 1 ), $obj->validate( $arr ) );
	}

	/**
	 * Verify the set_blog_language-method
	 */
	function test_set_blog_language() {
		$obj = $this->get_test();

		$arr = array( 'abc' => true, 'blog_language' => 'it_IT' );
		$this->assertEquals( array( 'abc' => true ), $obj->set_blog_language( $arr ) );
	}

}
