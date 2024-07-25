<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsFields;
use lloc\Msls\MslsJson;
use lloc\Msls\MslsMetaBox;
use lloc\Msls\MslsOptions;

class TestMslsMetaBox extends MslsUnitTestCase {


	protected function setUp(): void {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );

		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( array( $blog ) );

		$this->test = new MslsMetaBox( $options, $collection );
	}

	public function test_suggest(): void {
		$json = '{"some":"JSON"}';

		$post     = \Mockery::mock( 'WP_Post' );
		$post->ID = 42;

		Functions\expect( 'filter_has_var' )->times( 3 )->andReturnTrue();
		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, MslsFields::FIELD_BLOG_ID, FILTER_SANITIZE_NUMBER_INT )->andReturn( 17 );
		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, MslsFields::FIELD_POST_TYPE, FILTER_SANITIZE_FULL_SPECIAL_CHARS )->andReturn( 17 );
		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, MslsFields::FIELD_S, FILTER_SANITIZE_FULL_SPECIAL_CHARS )->andReturn( 17 );
		Functions\expect( 'get_post_stati' )->once()->andReturn( array( 'pending', 'draft', 'future' ) );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );
		Functions\expect( 'sanitize_text_field' )->times( 2 )->andReturnFirstArg();
		Functions\expect( 'get_posts' )->once()->andReturn( array( $post ) );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'wp_reset_postdata' )->once();

		Functions\when( 'wp_die' )->justEcho( $json );

		$this->expectOutputString( '{"some":"JSON"}' );

		MslsMetaBox::suggest();
	}

	public function test_get_suggested_fields_no_posts(): void {
		Functions\expect( 'wp_reset_postdata' )->once();
		Functions\expect( 'get_posts' )->once()->andReturn( array() );

		$json = \Mockery::mock( MslsJson::class );
		$args = array();

		$this->assertEquals( $json, MslsMetaBox::get_suggested_fields( $json, $args ) );
	}

	public function test_render_option_selected(): void {
		Functions\expect( 'selected' )->once()->andReturn( 'selected="selected"' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$this->assertEquals( '<option value="1" selected="selected">Test</option>', $this->test->render_option( 1, 1 ) );
	}

	public function test_render_option_not_selected(): void {
		Functions\expect( 'selected' )->once()->andReturn( '' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		$this->assertEquals( '<option value="1" >Test</option>', $this->test->render_option( 1, 2 ) );
	}

	public function test_render_options() {
		$post     = \Mockery::mock( 'WP_Post' );
		$post->ID = 42;

		Functions\expect( 'get_posts' )->once()->andReturn( array( $post ) );
		Functions\expect( 'get_post_stati' )->once()->andReturn( array( 'pending', 'draft', 'future' ) );
		Functions\expect( 'selected' )->once()->andReturn( 'selected="selected"' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'A random title' );

		$this->assertEquals( '<option value="42" selected="selected">A random title</option>', $this->test->render_options( 'post', 42 ) );
	}

	public function add_data_provider() {
		return array(
			array( array( 'post', 'page' ), 8, 8, true, true ),
			array( array( 'book' ), 3, 6, false, false ),
		);
	}

	/**
	 * @dataProvider add_data_provider
	 * @doesNotPerformAssertions
	 */
	public function test_add( $post_type, $fcount, $ocount, $content_import, $autocomplete ) {
		$options                          = \Mockery::mock( MslsOptions::class );
		$options->activate_content_import = $content_import;
		$options->activate_autocomplete   = $autocomplete;

		Functions\expect( 'get_post_types' )->andReturn( $post_type );
		Functions\expect( 'add_meta_box' )->times( $fcount );
		Functions\expect( '__' )->times( $fcount )->andReturnFirstArg();
		Functions\expect( 'msls_options' )->times( $ocount )->andReturn( $options );

		$this->test->add();
	}

	public function test_render_select_not_hierarchical() {
		global $post;

		$post     = \Mockery::mock( 'WP_Post' );
		$post->ID = 42;

		$post_type               = \Mockery::mock( \WP_Post_Type::class );
		$post_type->hierarchical = false;

		Functions\expect( 'get_post_type' )->once()->andReturn( 'page' );
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'wp_nonce_field' )->once()->andReturn( 'nonce_field' );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'esc_attr' )->times( 4 )->andReturnFirstArg();
		Functions\expect( 'esc_url' )->once()->andReturnFirstArg();
		Functions\expect( 'wp_kses' )->once()->andReturnFirstArg();
		Functions\expect( '__' )->once()->andReturnFirstArg();
		Functions\expect( 'add_query_arg' )->once()->andReturn( 'query_args' );
		Functions\expect( 'get_post_type_object' )->once()->andReturn( $post_type );
		Functions\expect( 'get_post_stati' )->once()->andReturn( array( 'draft', 'public', 'private' ) );
		Functions\expect( 'get_posts' )->once()->andReturn( array() );
		Functions\expect( 'get_current_blog_id' )->once()->andReturn( 1 );
		Functions\expect( 'get_admin_url' )->once()->andReturn( 'admin-url-empty' );

		$expected = '<ul><li><label for="msls_input_de_DE msls-icon-wrapper "><a title="Create a new translation in the de_DE-blog" href="admin-url-empty"><span class="language-badge de_DE"><span>de</span><span>DE</span></span></a>&nbsp;</label><select name="msls_input_de_DE"><option value="0"></option></select></li></ul>';
		$this->expectOutputString( $expected );

		$this->test->render_select();
	}

	public function test_render_select_hierarchical() {
		global $post;

		$post     = \Mockery::mock( 'WP_Post' );
		$post->ID = 42;

		$post_type               = \Mockery::mock( \WP_Post_Type::class );
		$post_type->hierarchical = true;

		Functions\expect( 'get_post_type' )->once()->andReturn( 'page' );
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );
		Functions\expect( 'wp_nonce_field' )->once()->andReturn( 'nonce_field' );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'esc_attr' )->times( 4 )->andReturnFirstArg();
		Functions\expect( 'esc_url' )->once()->andReturnFirstArg();
		Functions\expect( 'wp_kses' )->once()->andReturnFirstArg();
		Functions\expect( '__' )->once()->andReturnFirstArg();
		Functions\expect( 'add_query_arg' )->once()->andReturn( 'query_args' );
		Functions\expect( 'get_post_type_object' )->once()->andReturn( $post_type );
		Functions\expect( 'wp_dropdown_pages' )->once()->andReturn( '<select name="msls_input_region_Code"><option value="0">--some value</option></select>' );
		Functions\expect( 'get_edit_post_link' )->once()->andReturn( 'edit-post-link' );

		$expected = '<ul><li><label for="msls_input_de_DE msls-icon-wrapper "><a title="Edit the translation in the de_DE-blog" href="edit-post-link"><span class="language-badge de_DE"><span>de</span><span>DE</span></span></a>&nbsp;</label><select name="msls_input_region_Code"><option value="0">--some value</option></select></li></ul>';
		$this->expectOutputString( $expected );

		$this->test->render_select();
	}

	public function test_render_input() {
		global $post;

		$post     = \Mockery::mock( 'WP_Post' );
		$post->ID = 42;

		$post_type = \Mockery::mock( \WP_Post_Type::class );

		Functions\expect( 'get_post_types' )->once()->andReturn( array( 'post', 'page' ) );
		Functions\expect( 'get_post_type' )->once()->andReturn( 'page' );
		Functions\expect( 'get_the_title' )->once()->andReturn( 'Test' );

		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 2 ) . '/' );

		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );
		Functions\expect( 'wp_nonce_field' )->once()->andReturn( 'nonce_field' );
		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'esc_attr' )->times( 4 )->andReturnFirstArg();
		Functions\expect( 'esc_html' )->once()->andReturnFirstArg();
		Functions\expect( 'esc_url' )->once()->andReturnFirstArg();
		Functions\expect( '__' )->once()->andReturnFirstArg();
		Functions\expect( 'get_edit_post_link' )->once()->andReturn( 'edit-post-link' );

		$expected = '<ul><li class=""><label for="msls_title_ msls-icon-wrapper "><a title="Edit the translation in the de_DE-blog" href="edit-post-link"><span class="flag-icon flag-icon-de">de_DE</span></a>&nbsp;</label><input type="hidden" id="msls_id_" name="msls_input_de_DE" value="42"/><input class="msls_title" id="msls_title_" name="msls_title_" type="text" value="Test"/></li></ul><input type="hidden" name="msls_post_type" id="msls_post_type" value="page"/><input type="hidden" name="msls_action" id="msls_action" value="suggest_posts"/>';

		$this->expectOutputString( $expected );

		$this->test->render_input();
	}

	public function test_render_select_only_one_blog() {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( array() );

		Functions\expect( '__' )->once()->andReturnFirstArg();

		$this->test = new MslsMetaBox( $options, $collection );

		$expected = '<p>You should define at least another blog in a different language in order to have some benefit from this plugin!</p>';
		$this->expectOutputString( $expected );

		$this->test->render_select();
	}

	public function test_render_input_only_one_blog() {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( array() );

		Functions\expect( '__' )->once()->andReturnFirstArg();

		$this->test = new MslsMetaBox( $options, $collection );

		$expected = '<p>You should define at least another blog in a different language in order to have some benefit from this plugin!</p>';
		$this->expectOutputString( $expected );

		$this->test->render_input();
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function test_set() {
		Functions\expect( 'wp_is_post_revision' )->once()->andReturn( false );

		$this->test->set( 13 );
	}
}
