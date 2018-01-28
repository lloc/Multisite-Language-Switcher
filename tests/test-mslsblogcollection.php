<?php
/**
 * Tests for MslsBlogCollection
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;

/**
 * WP_Test_MslsBlogCollection
 */
class WP_Test_MslsBlogCollection extends Msls_UnitTestCase {

	/**
	 * Verify the instance-method
	 */
	function test_instance_method() {
		$obj =  MslsBlogCollection::instance();
		$this->assertInstanceOf( MslsBlogCollection::class, $obj );
		return $obj;
	}

	/**
	 * Verify the get_configured_blog_description-method
	 * @depends test_instance_method
	 */
	function test_get_description_method( $obj ) {
		$this->assertEquals( 'Test', $obj->get_configured_blog_description( 0, 'Test' ) );
		$this->assertEquals( false, $obj->get_configured_blog_description( 0, false ) );
	}

	/**
	 * Verify the test_get_blogs_of_reference_user-method
	 * @depends test_instance_method
	 */
	function test_get_blogs_of_reference_user_method( $obj ) {
		$options = $this->getMockBuilder( MslsOptions::class )->getMock();


		$this->assertInternalType( 'array', $obj->get_blogs_of_reference_user( $options ) );
	}

	/**
	 * Verify the get_current_blog_id-method
	 * @depends test_instance_method
	 */
	function test_get_current_blog_id_method( $obj ) {
		$this->assertInternalType( 'integer', $obj->get_current_blog_id() );
	}

	/**
	 * Verify the has_current_blog-method
	 * @depends test_instance_method
	 */
	function test_has_current_blog_method( $obj ) {
		$this->assertInternalType( 'boolean', $obj->has_current_blog() );
	}

	/**
	 * Verify the get_current_blog-method
	 * @depends test_instance_method
	 */
	function test_get_current_blog_method( $obj ) {
		// return MslsBlog|null
	}

	/**
	 * Verify the get_objects-method
	 * @depends test_instance_method
	 */
	function test_get_objects_method( $obj ) {
		$this->assertInternalType( 'array', $obj->get_objects() );
	}

	/**
	 * Verify the is_plugin_active-method
	 * @depends test_instance_method
	 */
	function test_is_plugin_active_method( $obj ) {
		$this->assertInternalType( 'boolean', $obj->is_plugin_active( 0 ) );
	}

	/**
	 * Verify the get_plugin_active_blogs-method
	 * @depends test_instance_method
	 */
	function test_get_plugin_active_blogs_method( $obj ) {
		$this->assertInternalType( 'array', $obj->get_plugin_active_blogs() );
	}

	/**
	 * Verify the get-method
	 * @depends test_instance_method
	 */
	function test_get_method( $obj ) {
		$this->assertInternalType( 'array', $obj->get() );
	}

	/**
	 * Verify the get_filtered-method
	 * @depends test_instance_method
	 */
	function test_get_filtered_method( $obj ) {
		$this->assertInternalType( 'array', $obj->get_filtered() );
	}

	/**
	 * Verify the get_users-method
	 * @depends test_instance_method
	 */
	function test_get_users_method( $obj ) {
		$this->assertInternalType( 'array', $obj->get_users() );
	}

	public function filter_available_languages( array $available_languages = array() ) {
		$available_languages[] = 'de_DE';

		return $available_languages;
	}

	/**
	 * Test get_blog_language
	 */
	public function test_get_blog_language() {
		$blog_id = $this->factory->blog->create();
		add_filter( 'get_available_languages', array( $this, 'filter_available_languages' ) );
		add_blog_option( $blog_id, 'WPLANG', 'de_DE' );
		$this->assertEquals( 'de_DE', MslsBlogCollection::get_blog_language( $blog_id ) );
	}

	/**
	 * Test get_blog_language when WPLANG option is missing
	 */
	public function test_get_blog_language_when_wplang_option_is_missing() {
		$blog_id = $this->factory->blog->create();
		delete_blog_option( $blog_id, 'WPLANG' );
		$this->assertEquals( 'en_US', MslsBlogCollection::get_blog_language( $blog_id ) );
	}
}
