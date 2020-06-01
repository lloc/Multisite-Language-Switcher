<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsAdminIconTaxonomy;
use Brain\Monkey\Functions;

class WP_Test_MslsAdminIconTaxonomy extends Msls_UnitTestCase {

	protected $lang = 'de_DE';

	protected $src = '/dev/german_flag.png';

	public function get_test() {
		return ( new MslsAdminIconTaxonomy( 'post_tag' ) )
			->set_path()
			->set_language( $this->lang )
			->set_src( $this->src );
	}

	function test_get_img() {
		Functions\expect( 'add_query_arg' )->twice()->andReturn( 'https://example.org/added-args' );
		Functions\expect( 'get_query_var' )->once()->andReturn( 'post_tag' );
		Functions\expect( 'get_taxonomies' )->once()->andReturn( [] );
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		$obj = $this->get_test();

		$this->assertEquals( '<img alt="de_DE" src="' . $this->src . '" />', $obj->get_img() );
	}

	function test_get_edit_new() {
		$admin_url = 'https://example.org/wp-admin/?new_tag';

		Functions\expect( 'add_query_arg' )->twice()->andReturn( 'https://example.org/added-args' );
		Functions\expect( 'get_admin_url' )->once()->andReturn( $admin_url );
		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );

		$obj = $this->get_test();

		$this->assertEquals( $admin_url, $obj->get_edit_new() );
	}

	function test_set_href() {
		Functions\expect( 'add_query_arg' )->twice()->andReturn( 'add-query-args' );
		Functions\expect( 'get_edit_term_link' )->once()->andReturn( 'get-edit-post-link' );

		$obj = $this->get_test();

		$this->assertInstanceOf( MslsAdminIconTaxonomy::class, $obj->set_href( 42 ) );
		$value = '<a title="Edit the translation in the de_DE-blog" href="get-edit-post-link"><span class="dashicons dashicons-edit"></span></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

	function test_set_href_empty() {
		Functions\expect( 'get_current_blog_id' )->twice()->andReturn( 1 );
		Functions\expect( 'add_query_arg' )->twice()->andReturn( 'add-query-args' );
		Functions\expect( 'get_edit_term_link' )->once()->andReturn( '' );
		Functions\expect( 'get_admin_url' )->twice()->andReturn( 'admin-url-empty' );

		$obj = $this->get_test();

		$this->assertInstanceOf( MslsAdminIconTaxonomy::class, $obj->set_href( 0 ) );

		$value = '<a title="Create a new translation in the de_DE-blog" href="admin-url-empty"><span class="dashicons dashicons-plus"></span></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

}
