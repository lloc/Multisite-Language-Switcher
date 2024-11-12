<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsOptionsTax;
use lloc\Msls\MslsPostTag;
use lloc\Msls\MslsTaxonomy;

class TestMslsPostTag extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\when( 'get_option' )->justReturn( array( 'de_DE' => 42 ) );
		Functions\expect( 'is_admin' )->andReturn( true );
		Functions\expect( 'get_post_types' )->andReturn( array( 'post', 'page' ) );

		foreach ( array( 'de_DE', 'en_US' ) as $locale ) {
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive( 'get_language' )->andReturn( $locale );

			$blogs[] = $blog;
		}

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_icon_type' )->andReturn( 'label' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( $blogs );
		$collection->shouldReceive( 'has_current_blog' )->andReturnTrue();
		$collection->shouldReceive( 'get_current_blog' )->andReturn( $blogs[0] );
		$collection->shouldReceive( 'get_blog_id' )->andReturnUsing(
			function ( $language ) {
				return $language === 'de_DE' ? 1 : null;
			}
		);
		$this->test = new MslsPostTag( $options, $collection );
	}

	public function test_init(): void {
		$options                        = \Mockery::mock( MslsOptions::class );
		$options->activate_autocomplete = true;

		$collection = \Mockery::mock( MslsBlogCollection::class );

		$taxonomy = \Mockery::mock( MslsTaxonomy::class );
		$taxonomy->shouldReceive( 'acl_request' )->once()->andReturn( 'post_tag' );

		Functions\expect( 'msls_options' )->atLeast()->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->atLeast()->once()->andReturn( $collection );
		Functions\expect( 'msls_content_types' )->atLeast()->once()->andReturn( $taxonomy );

		Actions\expectAdded( 'post_tag_edit_form_fields' )->once();
		Actions\expectAdded( 'post_tag_add_form_fields' )->once();
		Actions\expectAdded( 'edited_post_tag' )->once();
		Actions\expectAdded( 'create_post_tag' )->once();

		$this->expectNotToPerformAssertions();
		MslsPostTag::init();
	}

	/**
	 * Verify the static suggest-method
	 */
	public function test_suggest(): void {
		$term          = \Mockery::mock( \WP_Term::class );
		$term->term_id = 42;
		$term->name    = 'test';

		Functions\expect( 'wp_die' );
		Functions\expect( 'filter_has_var' )->atLeast()->once()->andReturnTrue();
		Functions\expect( 'filter_input' )->atLeast()->once()->andReturn( 'suggest_terms' );
		Functions\expect( 'switch_to_blog' )->atLeast()->once();
		Functions\expect( 'restore_current_blog' )->atLeast()->once();
		Functions\expect( 'get_terms' )->atLeast()->once()->andReturn( array( $term ) );

		self::expectOutputString( '' );

		MslsPostTag::suggest();
	}

	public function test_edit_input(): void {
		$taxonomy = \Mockery::mock( MslsTaxonomy::class );
		$taxonomy->shouldReceive( 'is_taxonomy' )->atLeast()->once()->andReturnTrue();
		$taxonomy->shouldReceive( 'get_request' )->atLeast()->once()->andReturn( 'post' );
		$taxonomy->shouldReceive( 'acl_request' )->atLeast()->once()->andReturn( array( 'taxonomy', 'post_tag' ) );

		$term       = \Mockery::mock( \WP_Term::class );
		$term->name = 'test-term-name';

		Functions\expect( 'msls_content_types' )->atLeast()->once()->andReturn( $taxonomy );
		Functions\expect( 'get_queried_object_id' )->atLeast()->once()->andReturn( 42 );
		Functions\expect( 'is_woocommerce' )->atLeast()->once()->andReturn( false );
		Functions\expect( 'switch_to_blog' )->atLeast()->once();
		Functions\expect( 'restore_current_blog' )->atLeast()->once();
		Functions\expect( 'add_query_arg' )->atLeast()->once()->andReturn( 'added_query_arg' );
		Functions\expect( 'get_current_blog_id' )->atLeast()->once()->andReturn( 23 );
		Functions\expect( 'get_admin_url' )->atLeast()->once()->andReturn( '/wp-admin/edit-tags.php' );
		Functions\expect( 'get_term' )->atLeast()->once()->andReturn( $term );
		Functions\expect( 'get_edit_term_link' )->atLeast()->once()->andReturn( 'edit_term_link' );

		$output = '<tr>
			<th colspan="2">
			<strong>Multisite Language Switcher</strong>
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="post"/>
			<input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/>
			</th>
			</tr><tr class="form-field">
			<th scope="row">
			<label for="msls_title_"><a title="Edit the translation in the de_DE-blog" href="edit_term_link"><span class="language-badge de_DE"><span>de</span><span>DE</span></span></a>&nbsp;</label>
			</th>
			<td>
			<input type="hidden" id="msls_id_" name="msls_input_de_DE" value="42"/>
			<input class="msls_title" id="msls_title_" name="msls_title_" type="text" value="test-term-name"/>
			</td>
			</tr><tr class="form-field">
			<th scope="row">
			<label for="msls_title_"><a title="Create a new translation in the en_US-blog" href="/wp-admin/edit-tags.php"><span class="language-badge en_US"><span>en</span><span>US</span></span></a>&nbsp;</label>
			</th>
			<td>
			<input type="hidden" id="msls_id_" name="msls_input_en_US" value=""/>
			<input class="msls_title" id="msls_title_" name="msls_title_" type="text" value=""/>
			</td>
			</tr>';

		$this->expectOutputString( $output );
		$this->test->edit_input( $term, 'test' );

		// second call should not output anything
		$this->test->edit_input( $term, 'test' );
	}

	public function test_add_input(): void {
		$taxonomy = \Mockery::mock( MslsTaxonomy::class );
		$taxonomy->shouldReceive( 'is_taxonomy' )->atLeast()->once()->andReturnTrue();
		$taxonomy->shouldReceive( 'get_request' )->atLeast()->once()->andReturn( 'post' );
		$taxonomy->shouldReceive( 'acl_request' )->atLeast()->once()->andReturn( array( 'taxonomy', 'post_tag' ) );

		Functions\expect( 'msls_content_types' )->atLeast()->once()->andReturn( $taxonomy );
		Functions\expect( 'get_queried_object_id' )->atLeast()->once()->andReturn( 42 );
		Functions\expect( 'is_woocommerce' )->atLeast()->once()->andReturn( false );
		Functions\expect( 'switch_to_blog' )->atLeast()->once();
		Functions\expect( 'restore_current_blog' )->atLeast()->once();
		Functions\expect( 'add_query_arg' )->atLeast()->once()->andReturn( 'added_query_arg' );
		Functions\expect( 'get_current_blog_id' )->atLeast()->once()->andReturn( 23 );
		Functions\expect( 'get_admin_url' )->atLeast()->once()->andReturn( '/wp-admin/edit-tags.php' );
		Functions\expect( 'get_term' )->atLeast()->once()->andReturnNull();

		Actions\expectDone( MslsPostTag::ADD_ACTION );

		$output = '<div class="form-field"><h3>Multisite Language Switcher</h3>
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="post"/>
			<input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/><label for="msls_title_"><a title="Create a new translation in the de_DE-blog" href="/wp-admin/edit-tags.php"><span class="language-badge de_DE"><span>de</span><span>DE</span></span></a>&nbsp;</label>
			<input type="hidden" id="msls_id_" name="msls_input_de_DE" value=""/>
			<input class="msls_title" id="msls_title_" name="msls_title_" type="text" value=""/><label for="msls_title_"><a title="Create a new translation in the en_US-blog" href="/wp-admin/edit-tags.php"><span class="language-badge en_US"><span>en</span><span>US</span></span></a>&nbsp;</label>
			<input type="hidden" id="msls_id_" name="msls_input_en_US" value=""/>
			<input class="msls_title" id="msls_title_" name="msls_title_" type="text" value=""/></div>';

		$this->expectOutputString( $output );

		$this->test->add_input( 'test' );

		// second call should not output anything
		$this->test->add_input( 'test' );
	}

	public function test_the_input_no_blogs(): void {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( array() );

		$value = ( new MslsPostTag( $options, $collection ) )->the_input( null, '', '' );

		$this->assertFalse( $value );
	}

	public function test_set() {
		$taxonomy = \Mockery::mock( MslsTaxonomy::class );
		$taxonomy->shouldReceive( 'acl_request' )->once()->andReturn( 'post_tag' );

		Functions\expect( 'msls_content_types' )->once()->andReturn( $taxonomy );
		Functions\expect( 'delete_option' )->twice();
		Functions\expect( 'switch_to_blog' )->twice();
		Functions\expect( 'restore_current_blog' )->twice();

		$this->expectNotToPerformAssertions();
		$this->test->set( 42 );
	}

	public function test_maybe_set_linked_term_origin_lang(): void {
		$mydata        = \Mockery::mock( MslsOptionsTax::class );
		$mydata->de_DE = 42;

		Functions\expect( 'filter_has_var' )->twice()->andReturnTrue();
		Functions\expect( 'filter_input' )->once()->andReturn( 'de_DE' );

		$result = $this->test->maybe_set_linked_term( $mydata );

		$this->assertSame( $mydata, $result );
	}

	public function test_maybe_set_linked_term_blog_id_null(): void {
		$mydata        = \Mockery::mock( MslsOptionsTax::class );
		$mydata->de_DE = 42;

		Functions\expect( 'filter_has_var' )->twice()->andReturnTrue();
		Functions\expect( 'filter_input' )->twice()->andReturn( 'fr_FR', 13 );

		$result = $this->test->maybe_set_linked_term( $mydata );

		$this->assertSame( $mydata, $result );
	}

	public function test_maybe_set_linked_term_origin_term_wrong(): void {
		$mydata        = \Mockery::mock( MslsOptionsTax::class );
		$mydata->en_US = 42;

		Functions\expect( 'filter_has_var' )->twice()->andReturnTrue();
		Functions\expect( 'filter_input' )->twice()->andReturn( 'de_DE', 13 );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'get_term' )->once()->andReturn( (object) array() );
		Functions\expect( 'restore_current_blog' )->once();

		$result = $this->test->maybe_set_linked_term( $mydata );

		$this->assertSame( $mydata, $result );
	}

	public function test_maybe_set_linked_term_happy_path(): void {
		$mydata        = \Mockery::mock( MslsOptionsTax::class );
		$mydata->en_US = 42;

		$term        = \Mockery::mock( \WP_Term::class );
		$term->en_US = 42;

		Functions\expect( 'filter_has_var' )->twice()->andReturnTrue();
		Functions\expect( 'filter_input' )->twice()->andReturn( 'de_DE', 13 );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'get_term' )->once()->andReturn( $term );
		Functions\expect( 'restore_current_blog' )->once();

		$result = $this->test->maybe_set_linked_term( $mydata );

		$expected = array( $term->en_US, 13 );
		$actual   = array( $result->en_US, $result->de_DE );

		$this->assertEquals( $expected, $actual );
	}
}
