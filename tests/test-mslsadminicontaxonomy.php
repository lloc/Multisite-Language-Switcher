<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsAdminIconTaxonomy;
use Brain\Monkey\Functions;

class WP_Test_MslsAdminIconTaxonomy extends Msls_UnitTestCase {

	const LANGUAGE = 'de_DE';
	const IMAGE_SRC = '/dev/german_flag.png';

	public function setUp(): void {
		parent::setUp();

		Functions\expect( 'add_query_arg' )->twice()->andReturn( 'https://example.org/added-args' );
	}

	public function test_get_img(): void {
		Functions\expect( 'get_query_var' )->once()->andReturn( 'post_tag' );
		Functions\expect( 'get_taxonomies' )->once()->andReturn( [] );

		$obj = ( new MslsAdminIconTaxonomy( 'post_tag' ) )->set_path()->set_language( self::LANGUAGE )->set_src(  self::IMAGE_SRC );

		$expected = sprintf( '<img alt="de_DE" src="%s" />', self::IMAGE_SRC );

		$this->assertEquals( $expected, $obj->get_img() );
	}

	public function test_get_edit_new(): void {
		$admin_url = 'https://example.org/wp-admin/?new_tag';

		Functions\expect( 'get_admin_url' )->once()->andReturn( $admin_url );
		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );

		$obj = ( new MslsAdminIconTaxonomy( 'post_tag' ) )->set_path()->set_language( self::LANGUAGE )->set_src(  self::IMAGE_SRC );

		$this->assertEquals( $admin_url, $obj->get_edit_new() );
	}

	public function test_set_href(): void {
		Functions\expect( 'get_edit_term_link' )->once()->andReturn( 'get-edit-post-link' );

		$obj = ( new MslsAdminIconTaxonomy( 'post_tag' ) )->set_path()->set_language( self::LANGUAGE )->set_src(  self::IMAGE_SRC );

		$this->assertInstanceOf( MslsAdminIconTaxonomy::class, $obj->set_href( 42 ) );

		$expected = '<a title="Edit the translation in the de_DE-blog" href="get-edit-post-link"><span class="dashicons dashicons-edit"></span></a>&nbsp;';

		$this->assertEquals( $expected, $obj->get_a() );
		$this->assertEquals( $expected, $obj->__toString() );
	}

	public function test_set_href_empty(): void {
		Functions\expect( 'get_current_blog_id' )->twice()->andReturn( 1 );
		Functions\expect( 'get_edit_term_link' )->once()->andReturn( '' );
		Functions\expect( 'get_admin_url' )->twice()->andReturn( 'admin-url-empty' );

		$obj = ( new MslsAdminIconTaxonomy( 'post_tag' ) )->set_path()->set_language( self::LANGUAGE )->set_src(  self::IMAGE_SRC );

		$this->assertInstanceOf( MslsAdminIconTaxonomy::class, $obj->set_href( 0 ) );

		$expected = '<a title="Create a new translation in the de_DE-blog" href="admin-url-empty"><span class="dashicons dashicons-plus"></span></a>&nbsp;';

		$this->assertEquals( $expected, $obj->get_a() );
		$this->assertEquals( $expected, $obj->__toString() );
	}

}
