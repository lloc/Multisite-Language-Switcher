<?php
/**
 * Tests for MslsAdminIconTaxonomy
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @package Msls
 */

/**
 * WP_Test_MslsAdminIconTaxonomy
 */
class WP_Test_MslsAdminIconTaxonomy extends Msls_UnitTestCase {

	/**
	 * Constructor
	 */
	function test_constructor_method() {
		$user_id = $this->factory->user->create( array( 'role' => 'editor' ) );
		$term_id = $this->factory->term->create();
		wp_set_current_user( $user_id );

		$obj = new MslsAdminIconTaxonomy( 'post_tag' );

		$this->assertInstanceOf( 'MslsAdminIconTaxonomy', $obj->set_path() );
		$this->assertInstanceOf( 'MslsAdminIconTaxonomy', $obj->set_language( 'de_DE' ) );
		$this->assertInstanceOf( 'MslsAdminIconTaxonomy', $obj->set_src( '/dev/german_flag.png' ) );

		$this->assertEquals( '<img alt="de_DE" src="/dev/german_flag.png" />', $obj->get_img() );
		$this->assertInternalType( 'string', $obj->get_edit_new() );

		$this->assertInstanceOf( 'MslsAdminIconTaxonomy', $obj->set_href( $term_id ) );
		$value = '<a title="Edit the translation in the de_DE-blog" href="http://example.org/wp-admin/edit-tags.php?action=edit&taxonomy=post_tag&tag_ID=' . $term_id . '&post_type=post"><img alt="de_DE" src="/dev/german_flag.png" /></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );

		$this->assertInstanceOf( 'MslsAdminIconTaxonomy', $obj->set_href( 0 ) );
		$value = '<a title="Create a new translation in the de_DE-blog" href="http://example.org/wp-admin/edit-tags.php?taxonomy=post_tag"><img alt="de_DE" src="/dev/german_flag.png" /></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );

		register_taxonomy( 'test_tax_cat', 'page' );
		$term_id = $this->factory->term->create( array( 'taxonomy' => 'test_tax_cat' ) );
		$obj = new MslsAdminIconTaxonomy( 'test_tax_cat' );

		$this->assertInstanceOf( 'MslsAdminIconTaxonomy', $obj->set_path() );
		$this->assertInstanceOf( 'MslsAdminIconTaxonomy', $obj->set_language( 'it_IT' ) );
		$this->assertInstanceOf( 'MslsAdminIconTaxonomy', $obj->set_src( '/dev/italian_flag.png' ) );

		$this->assertEquals( '<img alt="it_IT" src="/dev/italian_flag.png" />', $obj->get_img() );
		$this->assertInternalType( 'string', $obj->get_edit_new() );

		$this->assertInstanceOf( 'MslsAdminIconTaxonomy', $obj->set_href( $term_id ) );
		$value = '<a title="Edit the translation in the it_IT-blog" href="http://example.org/wp-admin/edit-tags.php?action=edit&taxonomy=test_tax_cat&tag_ID=' . $term_id . '&post_type=page"><img alt="it_IT" src="/dev/italian_flag.png" /></a>&nbsp;';
		$this->assertEquals( $value, $obj->get_a() );
		$this->assertEquals( $value, $obj->__toString() );
	}

}
