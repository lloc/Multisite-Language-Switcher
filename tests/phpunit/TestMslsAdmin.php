<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsAdmin;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;

class TestMslsAdmin extends MslsUnitTestCase {

	/**
	 * @param array $users
	 * @return MslsAdmin
	 */
	public function get_sut( array $users = array() ): MslsAdmin {
		Functions\when( 'get_option' )->justReturn( array() );
		Functions\when( 'update_option' )->justReturn( true );
		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
		Functions\when( 'checked' )->justReturn( '' );
		Functions\when( 'selected' )->justReturn( '' );
		Functions\when( 'get_admin_url' )->justReturn( 'wp-admin' );
		Functions\when( 'get_locale' )->justReturn( 'de_DE' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_empty' )->andReturns( false );
		$options->shouldReceive( 'get_available_languages' )->andReturns( array( 'de_DE', 'it_IT' ) );
		$options->shouldReceive( 'get_icon_type' )->andReturns( 'flag' );

		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_title' )->andReturns( 'abc (DEF)' );
		$blog->shouldReceive( 'get_description' )->andReturns( 'DEF' );
		$blog->userblog_id = 1;
		$blog->blogname    = 'abc';

		$blogs[] = $blog;

		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_title' )->andReturns( 'uvw (XYZ)' );
		$blog->shouldReceive( 'get_description' )->andReturns( 'XYZ' );
		$blog->userblog_id = 2;
		$blog->blogname    = 'uvw';

		$blogs[] = $blog;
		if ( empty( $users ) ) {
			$users = array(
				(object) array(
					'ID'            => 1,
					'user_nicename' => 'realloc',
				),
			);
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog_id' )->andReturns( 1 );
		$collection->shouldReceive( 'get_plugin_active_blogs' )->andReturns( $blogs );
		$collection->shouldReceive( 'get_users' )->andReturns( $users );

		return new MslsAdmin( $options, $collection );
	}

	public function test_has_problems_no_problem(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_available_languages' )->andReturns( array( 'de_DE', 'it_IT' ) );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$options->shouldReceive( 'is_empty' )->andReturns( false );

		$obj = new MslsAdmin( $options, $collection );

		$this->assertFalse( $obj->has_problems() );
	}

	public function test_has_problems_one_language(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_available_languages' )->andReturns( array( 'de_DE' ) );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$options->shouldReceive( 'is_empty' )->andReturns( false );

		$obj = new MslsAdmin( $options, $collection );

		$this->expectOutputRegex( '/^<div id="msls-warning" class="updated fade"><p>.*$/' );

		$this->assertTrue( $obj->has_problems() );
	}

	public function test_has_problems_is_empty(): void {
		Functions\when( 'get_option' )->justReturn( array() );
		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
		Functions\when( 'admin_url' )->justReturn( '' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_empty' )->andReturns( true );

		$collection = \Mockery::mock( MslsBlogCollection::class );

		$obj = new MslsAdmin( $options, $collection );

		$this->expectOutputRegex( '/^<div id="msls-warning" class="updated fade"><p>.*$/' );

		$this->assertTrue( $obj->has_problems() );
	}

	public function test_subsubsub(): void {
		$obj = $this->get_sut();

		$this->assertEquals(
			'<ul class="subsubsub"><li><a href="wp-admin" class="current">abc (DEF)</a> | </li><li><a href="wp-admin">uvw (XYZ)</a></li></ul>',
			$obj->subsubsub()
		);
	}

	public function test_blog_language(): void {
		$obj = $this->get_sut();

		$this->expectOutputRegex( '/^<select id="blog_language" name="msls\[blog_language\]">.*$/' );
		$obj->blog_language();
	}

	public function test_display(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<select id="display" name="msls[display]"><option value="0" >Flag and description</option><option value="1" >Description only</option><option value="2" >Flag only</option><option value="3" >Description and flag</option></select>'
		);
		$obj->display();
	}

	public function test_admin_display(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<select id="admin_display" name="msls[admin_display]"><option value="flag" >Flag</option><option value="label" >Label</option></select>'
		);
		$obj->admin_display();
	}

	public function test_reference_user(): void {
		$users    = array();
		$too_much = MslsAdmin::MAX_REFERENCE_USERS + 1;
		for ( $i = 1; $i <= $too_much; $i++ ) {
			$users[] = (object) array(
				'ID'            => $i,
				'user_nicename' => 'realloc',
			);
		}

		$obj = $this->get_sut( $users );

		set_error_handler(
			static function ( $errno, $errstr ) {
				restore_error_handler();
				throw new \Exception( $errstr, $errno );
			},
			E_ALL
		);

		$this->expectException( \Exception::class );
		$this->expectExceptionMessage(
			'Multisite Language Switcher: Collection for reference user has been truncated because it exceeded the maximum of 100 users. Please, use the hook "msls_reference_users" to filter the result before!'
		);

		$obj->reference_user();
	}

	public function test_reference_user_over_max(): void {
		$obj = $this->get_sut();

		$this->expectOutputRegex( '/^<select id="reference_user" name="msls\[reference_user\]">.*$/' );
		$obj->reference_user();
	}

	public function test_activate_autocomplete(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="checkbox" id="activate_autocomplete" name="msls[activate_autocomplete]" value="1" /> <label for="activate_autocomplete">Activate experimental autocomplete inputs</label>'
		);
		$obj->activate_autocomplete();
	}

	public function test_sort_by_description(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="checkbox" id="sort_by_description" name="msls[sort_by_description]" value="1" /> <label for="sort_by_description">Sort languages by description</label>'
		);
		$obj->sort_by_description();
	}


	public function test_exclude_current_blog(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="checkbox" id="exclude_current_blog" name="msls[exclude_current_blog]" value="1" /> <label for="exclude_current_blog">Exclude this blog from output</label>'
		);
		$obj->exclude_current_blog();
	}

	function test_only_with_translation(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="checkbox" id="only_with_translation" name="msls[only_with_translation]" value="1" /> <label for="only_with_translation">Show only links with a translation</label>'
		);
		$obj->only_with_translation();
	}

	function test_output_current_blog(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="checkbox" id="output_current_blog" name="msls[output_current_blog]" value="1" /> <label for="output_current_blog">Display link to the current language</label>'
		);
		$obj->output_current_blog();
	}

	function test_description(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="description" name="msls[description]" value="" size="40"/>'
		);
		$obj->description();
	}

	function test_before_output(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="before_output" name="msls[before_output]" value="" size="30"/>'
		);
		$obj->before_output();
	}

	function test_after_output(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="after_output" name="msls[after_output]" value="" size="30"/>'
		);
		$obj->after_output();
	}

	function test_before_item(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="before_item" name="msls[before_item]" value="" size="30"/>'
		);
		$obj->before_item();
	}

	function test_after_item(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="after_item" name="msls[after_item]" value="" size="30"/>'
		);
		$obj->after_item();
	}

	function test_rewrite_tizio(): void {
		$obj = $this->get_sut();

		$post_type          = \Mockery::mock( \WP_Post_Type::class );
		$post_type->rewrite = false;

		Functions\when( 'get_post_type_object' )->justReturn( $post_type );

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="rewrite_tizio" name="msls[rewrite_tizio]" value="" size="30" readonly="readonly"/>'
		);
		$obj->rewrite_tizio( 'tizio' );
	}

	function test_rewrite_pinko(): void {
		$obj = $this->get_sut();

		$post_type          = \Mockery::mock( \WP_Post_Type::class );
		$post_type->rewrite = true;

		Functions\when( 'get_post_type_object' )->justReturn( $post_type );

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="rewrite_pinko" name="msls[rewrite_pinko]" value="pinko" size="30" readonly="readonly"/>'
		);
		$obj->rewrite_pinko( 'pinko' );
	}

	function test_rewrite_pallino(): void {
		$obj = $this->get_sut();

		$post_type          = \Mockery::mock( \WP_Post_Type::class );
		$post_type->rewrite = array( 'slug' => 'pallino_slug' );

		Functions\when( 'get_post_type_object' )->justReturn( $post_type );

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="rewrite_pallino" name="msls[rewrite_pallino]" value="pallino_slug" size="30" readonly="readonly"/>'
		);
		$obj->rewrite_pallino( 'pallino' );
	}

	function test_content_filter(): void {
		$obj = $this->get_sut();

		$this->expectOutputString(
			'<input type="checkbox" id="content_filter" name="msls[content_filter]" value="1" /> <label for="content_filter">Add hint for available translations</label>'
		);
		$obj->content_filter();
	}

	function test_content_priority(): void {
		$obj = $this->get_sut();

		$this->expectOutputRegex( '/^<select id="content_priority" name="msls\[content_priority\]">.*$/' );
		$obj->content_priority();
	}

	function test_validate(): void {
		$obj = $this->get_sut();

		$arr = array();
		$this->assertEquals( array( 'display' => 0 ), $obj->validate( $arr ) );
		$arr = array(
			'image_url' => '/test/',
			'display'   => '1',
		);
		$this->assertEquals(
			array(
				'image_url' => '/test',
				'display'   => 1,
			),
			$obj->validate( $arr )
		);
	}

	function test_set_blog_language(): void {
		$obj = $this->get_sut();

		$arr = array(
			'abc'           => true,
			'blog_language' => 'it_IT',
		);
		$this->assertEquals( array( 'abc' => true ), $obj->set_blog_language( $arr ) );
	}

	function test_render(): void {
		$obj = $this->get_sut();

		Functions\expect( 'settings_fields' )->once();
		Functions\expect( 'do_settings_sections' )->once();

		$this->expectOutputRegex(
			'/^<div class="wrap"><div class="icon32" id="icon-options-general"><br\/><\/div><h1>Multisite Language Switcher Options<\/h1>.*$/'
		);
		$obj->render();
	}

	function test_language_section(): void {
		$obj = $this->get_sut();

		Functions\when( 'add_settings_field' )->returnArg();

		$this->assertEquals( 1, $obj->language_section() );
	}

	function test_main_section(): void {
		$obj = $this->get_sut();

		Functions\expect( 'add_settings_field' )->times( 12 )->andReturnFirstArg();
		Functions\expect( 'esc_html__' )->times( 12 )->andReturnFirstArg();

		$this->assertEquals( 12, $obj->main_section() );
	}

	function test_advanced_section(): void {
		$obj = $this->get_sut();

		Functions\expect( 'add_settings_field' )->times( 5 )->andReturnFirstArg();
		Functions\expect( 'esc_html__' )->times( 5 )->andReturnFirstArg();

		$this->assertEquals( 5, $obj->advanced_section() );
	}

	function test_rewrites_section(): void {
		$obj = $this->get_sut();

		foreach ( array(
			'post' => 'Post',
			'page' => 'Page',
		) as $name => $label ) {
			$post_type        = \Mockery::mock( \WP_Post_Type::class );
			$post_type->name  = $name;
			$post_type->label = $label;

			$post_types[ $name ] = $post_type;
		}

		Functions\when( 'get_post_types' )->justReturn( $post_types );
		Functions\when( 'add_settings_field' )->returnArg();

		$this->assertEquals( 2, $obj->rewrites_section() );
	}

	public function test_register(): void {
		global $wp_rewrite;

		Functions\expect( 'register_setting' )->once();
		Functions\expect( 'add_settings_section' )->times( 4 );

		$wp_rewrite = \Mockery::mock( '\WP_Rewrite' );
		$wp_rewrite->shouldReceive( 'using_permalinks' )->andReturnTrue();

		$obj = $this->get_sut();

		$this->expectNotToPerformAssertions();
		$obj->register();
	}
}
