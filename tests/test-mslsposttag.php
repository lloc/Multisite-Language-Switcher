<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsPostTag;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;
use Brain\Monkey\Functions;
use function Patchwork\always;
use function Patchwork\redefine;

/**
 * WP_Test_MslsPostTag
 */
class WP_Test_MslsPostTag extends Msls_UnitTestCase {

	public function get_terms(): array {
		return [
			(object) [
				'term_id' => 123,
				'name' => 'pinko',
			]
		];
	}

	public function get_sut() {
		Functions\stubs( [
			'get_queried_object_id' => 1,
			'is_admin' => true,
			'get_option' => [ 'exists' => true ],
			'get_post_types' => [ 'exists' => true ],
			'switch_to_blog' => true,
			'restore_current_blog' => true,
			'plugin_dir_path' => dirname( __DIR__, 1 ) . '/',
			'get_current_blog_id' => 1,
			'get_admin_url' => '/admin_url',
		] );

		$options = \Mockery::mock( MslsOptions::class );

		$map = [ 'de_DE', 'en_US' ];

		foreach ( $map as $locale ) {
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive( ['get_language' => $locale ] );

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( $blogs );


		return new MslsPostTag( $options, $collection );
	}
	/**
	 * Verify the static suggest-method
	 */
	function test_suggest(): void {
		$terms = [
			(object) [
				'term_id' => 123,
				'name' => 'pinko',
			]
		];

		Functions\expect( 'wp_die' );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'sanitize_text_field' )->andReturnFirstArg();
		Functions\expect( 'get_terms' )->once()->andReturn( $terms );

		redefine( 'filter_has_var', always( true ) );
		redefine( 'filter_input', always( '' ) );

		$this->assertNull( MslsPostTag::suggest() );
	}

	/**
	 * Verify the static suggest-method
	 */
	function test_suggest_method_no_filter_vars() {
		Functions\expect( 'wp_die' );

		redefine( 'filter_has_var', always( false ) );

		$this->assertNull( MslsPostTag::suggest() );
	}

	public function test_add_input() {
		$expected = '<div class="form-field"><h3>Multisite Language Switcher</h3><input type="hidden" name="msls_post_type" id="msls_post_type" value="post"/><input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/><label for="msls_title_"><a title="Create a new translation in the de_DE-blog" href="/admin_url"><span class="flag-icon flag-icon-de">de_DE</span></a>&nbsp;</label><input type="hidden" id="msls_id_" name="msls_input_de_DE" value=""/><input class="msls_title" id="msls_title_" name="msls_title_" type="text" value=""/><label for="msls_title_"><a title="Create a new translation in the en_US-blog" href="/admin_url"><span class="flag-icon flag-icon-us">en_US</span></a>&nbsp;</label><input type="hidden" id="msls_id_" name="msls_input_en_US" value=""/><input class="msls_title" id="msls_title_" name="msls_title_" type="text" value=""/></div>';

		$this->expectOutputString( $expected );

		$this->get_sut()->add_input( null );
	}

	public function test_edit_input() {
		$expected = '<tr><th colspan="2"><strong>Multisite Language Switcher</strong><input type="hidden" name="msls_post_type" id="msls_post_type" value="post"/><input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/></th></tr><tr class="form-field"><th scope="row" vertical-align="top"><label for="msls_title_"><a title="Create a new translation in the de_DE-blog" href="/admin_url"><span class="flag-icon flag-icon-de">de_DE</span></a>&nbsp;</label></th><td><input type="hidden" id="msls_id_" name="msls_input_de_DE" value=""/><input class="msls_title" id="msls_title_" name="msls_title_" type="text" value=""/></td></tr><tr class="form-field"><th scope="row" vertical-align="top"><label for="msls_title_"><a title="Create a new translation in the en_US-blog" href="/admin_url"><span class="flag-icon flag-icon-us">en_US</span></a>&nbsp;</label></th><td><input type="hidden" id="msls_id_" name="msls_input_en_US" value=""/><input class="msls_title" id="msls_title_" name="msls_title_" type="text" value=""/></td></tr>';

		$this->expectOutputString( $expected );

		$this->get_sut()->edit_input( null );
	}

	public function test_the_input_false() {
		$options    = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( [] );

		$sut = new MslsPostTag( $options, $collection );

		$this->assertFalse( $sut->the_input( null, 'test', 'test' ) );
	}

	public function test_set(): void {
		$this->assertNull( $this->get_sut()->set( 1 ) );
	}
}
