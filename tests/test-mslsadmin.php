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

/**
 * WP_Test_MslsAdmin
 */
class WP_Test_MslsAdmin extends Msls_UnitTestCase {

	function get_test() {
		$options    = MslsOptions::instance();
		$collection = MslsBlogCollection::instance();

		return new MslsAdmin( $options, $collection );
	}

	/**
	 * Verify the has_problems-method
	 */
	function test_has_problems() {
		$obj = $this->get_test();

		$this->expectOutputRegex( '/^<div id="msls-warning" class="updated fade"><p>.*$/' );
		$retval = $obj->has_problems();

		$this->assertInternalType( 'bool', $retval );
	}

	/**
	 * Verify the subsubsub-method
	 */
	function test_subsubsub() {
		$obj = $this->get_test();

		$this->assertInternalType( 'string', $obj->subsubsub() );
	}

	/**
	 * Verify the blog_language-method
	 */
	function test_blog_language() {
		$obj = $this->get_test();

		$this->expectOutputRegex( '/^<select id="blog_language" name="msls\[blog_language\]">.*$/' );
		$obj->blog_language();
	}

	/**
	 * Verify the admin_language-method
	 */
	function test_admin_language() {
		$obj = $this->get_test();

		$this->expectOutputRegex( '/^<select id="admin_language" name="msls\[admin_language\]">.*$/' );
		$obj->admin_language();
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

		$this->expectOutputString( '<input type="checkbox" id="activate_autocomplete" name="msls[activate_autocomplete]" value="1" />' );
		$obj->activate_autocomplete();
	}

	/**
	 * Verify the sort_by_description-method
	 */
	function test_sort_by_description() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="sort_by_description" name="msls[sort_by_description]" value="1" />' );
		$obj->sort_by_description();
	}

	/**
	 * Verify the exclude_current_blog-method
	 */
	function test_exclude_current_blog() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="exclude_current_blog" name="msls[exclude_current_blog]" value="1" />' );
		$obj->exclude_current_blog();
	}

	/**
	 * Verify the only_with_translation-method
	 */
	function test_only_with_translation() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="only_with_translation" name="msls[only_with_translation]" value="1" />' );
		$obj->only_with_translation();
	}

	/**
	 * Verify the output_current_blog-method
	 */
	function test_output_current_blog() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="output_current_blog" name="msls[output_current_blog]" value="1" />' );
		$obj->output_current_blog();
	}

	/**
	 * Verify the description-method
	 */
	function test_description() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input id="description" name="msls[description]" value="" size="40"/>' );
		$obj->description();
	}

	/**
	 * Verify the content_filter-method
	 */
	function test_content_filter() {
		$obj = $this->get_test();

		$this->expectOutputString( '<input type="checkbox" id="content_filter" name="msls[content_filter]" value="1" />' );
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
