<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsPostTagClassic;

class TestMslsPostTagClassic extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\expect( 'get_option' )->andReturn(
			array(
				'de_DE' => 42,
				'en_US' => 23,
			)
		);
		Functions\expect( 'is_admin' )->andReturnTrue();
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

		$this->test = new MslsPostTagClassic( $options, $collection );
	}

	/**
	 * Verify the static suggest-method
	 */
	public function test_suggest(): void {
		Functions\expect( 'wp_die' );

		self::expectOutputString( '' );

		MslsPostTagClassic::suggest();
	}

	public function test_edit_input(): void {
		$terms = array(
			(object) array(
				'term_id' => 42,
				'name'    => 'Term 42',
			),
			(object) array(
				'term_id' => 23,
				'name'    => 'Term 23',
			),
		);

		Functions\expect( 'get_queried_object_id' )->atLeast()->once()->andReturn( 42 );
		Functions\expect( 'switch_to_blog' )->atLeast()->once();
		Functions\expect( 'restore_current_blog' )->atLeast()->once();
		Functions\expect( 'get_terms' )->atLeast()->once()->andReturn( $terms );
		Functions\expect( 'is_woocommerce' )->atLeast()->once()->andReturn( false );
		Functions\expect( 'add_query_arg' )->atLeast()->once()->andReturn( 'added_query_arg' );
		Functions\expect( 'selected' )->atLeast()->once()->andReturn( '' );
		Functions\expect( 'get_edit_term_link' )->atLeast()->once()->andReturn( 'edit_term_link' );

		$taxonomy = \Mockery::mock( \WP_Taxonomy::class );
		$taxonomy->shouldReceive( 'acl_request' )->atLeast()->once()->andReturn( array( 'taxonomy', 'post_type' ) );
		$taxonomy->shouldReceive( 'is_taxonomy' )->atLeast()->once()->andReturnTrue();
		$taxonomy->shouldReceive( 'get_request' )->atLeast()->once()->andReturn( 'post_type' );

		Functions\expect( 'msls_content_types' )->atLeast()->once()->andReturn( $taxonomy );

		$output = '<tr>
			<th colspan="2">
			<strong>Multisite Language Switcher</strong>
			</th>
			</tr><tr class="form-field">
			<th scope="row">
			<label for="msls_input_de_DE"><a title="Edit the translation in the de_DE-blog" href="edit_term_link"><span class="language-badge de_DE"><span>de</span><span>DE</span></span></a>&nbsp;</label></th>
			<td>
			<select class="msls-translations" name="msls_input_de_DE">
			<option value=""></option>
			<option value="42" >Term 42</option><option value="23" >Term 23</option>
			</select></td>
			</tr><tr class="form-field">
			<th scope="row">
			<label for="msls_input_en_US"><a title="Edit the translation in the en_US-blog" href="edit_term_link"><span class="language-badge en_US"><span>en</span><span>US</span></span></a>&nbsp;</label></th>
			<td>
			<select class="msls-translations" name="msls_input_en_US">
			<option value=""></option>
			<option value="42" >Term 42</option><option value="23" >Term 23</option>
			</select></td>
			</tr>';

		self::expectOutputString( $output );

		$tag = \Mockery::mock( \WP_Term::class );

		$this->test->edit_input( $tag, 'test' );

		// second call should not output anything
		$this->test->edit_input( $tag, 'test' );
	}

	public function test_add_input(): void {
		$terms = array(
			(object) array(
				'term_id' => 42,
				'name'    => 'Term 42',
			),
			(object) array(
				'term_id' => 23,
				'name'    => 'Term 23',
			),
		);

		Functions\expect( 'get_queried_object_id' )->atLeast()->once()->andReturn( 42 );
		Functions\expect( 'switch_to_blog' )->atLeast()->once();
		Functions\expect( 'restore_current_blog' )->atLeast()->once();
		Functions\expect( 'get_terms' )->atLeast()->once()->andReturn( $terms );
		Functions\expect( 'is_woocommerce' )->atLeast()->once()->andReturn( false );
		Functions\expect( 'add_query_arg' )->atLeast()->once()->andReturn( 'added_query_arg' );
		Functions\expect( 'selected' )->atLeast()->once()->andReturn( '' );
		Functions\expect( 'get_edit_term_link' )->atLeast()->once()->andReturn( 'edit_term_link' );

		$taxonomy = \Mockery::mock( \WP_Taxonomy::class );
		$taxonomy->shouldReceive( 'acl_request' )->atLeast()->once()->andReturn( array( 'taxonomy', 'post_type' ) );
		$taxonomy->shouldReceive( 'is_taxonomy' )->atLeast()->once()->andReturnTrue();
		$taxonomy->shouldReceive( 'get_request' )->atLeast()->once()->andReturn( 'post_type' );

		Functions\expect( 'msls_content_types' )->atLeast()->once()->andReturn( $taxonomy );

		$output = '<div class="form-field"><h3>Multisite Language Switcher</h3><label for="msls_input_de_DE"><a title="Edit the translation in the de_DE-blog" href="edit_term_link"><span class="language-badge de_DE"><span>de</span><span>DE</span></span></a>&nbsp;</label>
			<select class="msls-translations" name="msls_input_de_DE">
			<option value=""></option>
			<option value="42" >Term 42</option><option value="23" >Term 23</option>
			</select><label for="msls_input_en_US"><a title="Edit the translation in the en_US-blog" href="edit_term_link"><span class="language-badge en_US"><span>en</span><span>US</span></span></a>&nbsp;</label>
			<select class="msls-translations" name="msls_input_en_US">
			<option value=""></option>
			<option value="42" >Term 42</option><option value="23" >Term 23</option>
			</select></div>';

		$this->expectOutputString( $output );

		$this->test->add_input( 'test' );
	}

	public function test_the_input_no_blogs(): void {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( array() );

		$value = ( new MslsPostTagClassic( $options, $collection ) )->the_input( null, '', '' );

		$this->assertFalse( $value );
	}
}
