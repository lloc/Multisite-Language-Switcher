<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsPostTag;

class TestMslsPostTag extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\when( 'get_option' )->justReturn( array() );
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

		$this->test = new MslsPostTag( $options, $collection );
	}

	/**
	 * Verify the static suggest-method
	 */
	public function test_suggest(): void {
		Functions\expect( 'wp_die' );

		self::expectOutputString( '' );

		MslsPostTag::suggest();
	}

	public function test_edit_input(): void {
		Functions\expect( 'did_action' )->andReturn( 1 );
		Functions\expect( 'get_queried_object_id' )->andReturn( 42 );
		Functions\expect( 'get_current_blog_id' )->andReturn( 23 );
		Functions\expect( 'get_admin_url' )->andReturn( '/wp-admin/edit-tags.php' );
		Functions\expect( 'switch_to_blog' )->atLeast();
		Functions\expect( 'restore_current_blog' )->atLeast();
		Functions\expect( 'get_terms' )->andReturn( array() );
		Functions\expect( 'plugin_dir_path' )->atLeast( 1 )->andReturn( dirname( __DIR__, 1 ) . '/' );
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$output = '<tr>
			<th colspan="2">
			<strong>Multisite Language Switcher</strong>
			<input type="hidden" name="msls_post_type" id="msls_post_type" value="post"/>
			<input type="hidden" name="msls_action" id="msls_action" value="suggest_terms"/>
			</th>
			</tr><tr class="form-field">
			<th scope="row">
			<label for="msls_title_"><a title="Create a new translation in the de_DE-blog" href="/wp-admin/edit-tags.php"><span class="language-badge de_DE"><span>de</span><span>DE</span></span></a>&nbsp;</label>
			</th>
			<td>
			<input type="hidden" id="msls_id_" name="msls_input_de_DE" value=""/>
			<input class="msls_title" id="msls_title_" name="msls_title_" type="text" value=""/>
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

		$tag = \Mockery::mock( \WP_Term::class );

		$this->test->edit_input( $tag, 'test' );

		// second call should not output anything
		$this->test->edit_input( $tag, 'test' );
	}

	public function test_add_input() {
		$this->expectOutputString( '<div class="form-field"></div>' );
		$this->test->add_input( 'test' );
	}
}
