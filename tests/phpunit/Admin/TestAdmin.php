<?php declare( strict_types=1 );

namespace lloc\MslsTests\Admin;

use Brain\Monkey\Functions;
use lloc\Msls\Admin\Admin;
use lloc\Msls\Blog\Blog;
use lloc\Msls\Blog\Collection;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestAdmin extends MslsUnitTestCase {

	private function AdminFactory( array $users = array() ): Admin {
		Functions\when( 'get_option' )->justReturn( array() );
		Functions\when( 'update_option' )->justReturn( true );
		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
		Functions\when( 'checked' )->justReturn( '' );
		Functions\when( 'selected' )->justReturn( '' );
		Functions\when( 'get_admin_url' )->justReturn( 'wp-admin' );
		Functions\when( 'get_locale' )->justReturn( 'de_DE' );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_empty' )->andReturns( false );
		$options->shouldReceive( 'get_available_languages' )->andReturns( array( 'de_DE', 'it_IT' ) );
		$options->shouldReceive( 'get_icon_type' )->andReturns( 'flag' );

		$blog = \Mockery::mock( Blog::class );
		$blog->shouldReceive( 'get_title' )->andReturns( 'abc (DEF)' );
		$blog->shouldReceive( 'get_description' )->andReturns( 'DEF' );
		$blog->userblog_id = 1;
		$blog->blogname    = 'abc';

		$blogs[] = $blog;

		$blog = \Mockery::mock( Blog::class );
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

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_current_blog_id' )->andReturns( 1 );
		$collection->shouldReceive( 'get_plugin_active_blogs' )->andReturns( $blogs );
		$collection->shouldReceive( 'get_users' )->andReturns( $users );

		return new Admin( $options, $collection );
	}

	/**
	 * @return array<string, array{array<string>, bool, string}>
	 */
	public static function has_problems_provider(): array {
		$warning = '/^<div id="msls-warning" class="updated fade"><p>.*$/';

		return array(
			'two languages, options filled' => array( array( 'de_DE', 'it_IT' ), false, '/^$/' ),
			'only one language'             => array( array( 'de_DE' ), false, $warning ),
			'no languages, options empty'   => array( array(), true, $warning ),
		);
	}

	#[DataProvider( 'has_problems_provider' )]
	public function test_has_problems( array $languages, bool $is_empty, string $regex ): void {
		Functions\when( 'get_option' )->justReturn( array() );
		Functions\when( 'get_current_blog_id' )->justReturn( 1 );
		Functions\when( 'admin_url' )->justReturn( '' );

		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_available_languages' )->zeroOrMoreTimes()->andReturns( $languages );

		$collection = \Mockery::mock( Collection::class );
		$options->shouldReceive( 'is_empty' )->once()->andReturns( $is_empty );

		$this->expectOutputRegex( $regex );

		( new Admin( $options, $collection ) )->has_problems();
	}

	public function test_subsubsub(): void {
		$obj = $this->AdminFactory();

		$expected = '<ul class="subsubsub"><li><a href="wp-admin" class="current">abc (DEF)</a> | </li><li><a href="wp-admin">uvw (XYZ)</a></li></ul>';

		$this->assertEquals( $expected, $obj->subsubsub() );
	}

	public function test_blog_language(): void {
		$obj = $this->AdminFactory();

		$this->expectOutputRegex( '/^<select id="blog_language" name="msls\[blog_language\]">.*$/' );
		$obj->blog_language();
	}

	public function test_display(): void {
		$obj = $this->AdminFactory();

		$this->expectOutputString(
			'<select id="display" name="msls[display]"><option value="0" >Flag and description</option><option value="1" >Description only</option><option value="2" >Flag only</option><option value="3" >Description and flag</option></select>'
		);
		$obj->display();
	}

	public function test_admin_display(): void {
		$obj = $this->AdminFactory();

		$this->expectOutputString(
			'<select id="admin_display" name="msls[admin_display]"><option value="flag" >Flag</option><option value="label" >Label</option></select>'
		);
		$obj->admin_display();
	}

	public function test_reference_user(): void {
		Functions\expect( 'wp_list_pluck' )->once()->andReturn( array( 1 => 'realloc' ) );

		$obj = $this->AdminFactory();

		ob_start();
		$obj->reference_user();
		$output = (string) ob_get_clean();

		$this->assertStringStartsWith( '<select id="reference_user" name="msls[reference_user]">', $output );
		$this->assertStringNotContainsString( 'class="description"', $output );
	}

	public function test_reference_user_over_max(): void {
		$users = array();
		for ( $i = 1; $i <= Admin::MAX_REFERENCE_USERS + 1; $i++ ) {
			$users[] = (object) array(
				'ID'            => $i,
				'user_nicename' => 'user-' . $i,
			);
		}

		Functions\expect( 'wp_list_pluck' )->once()->andReturnUsing(
			function ( array $list ): array {
				$this->assertCount( Admin::MAX_REFERENCE_USERS, $list );

				return array_column( $list, 'user_nicename', 'ID' );
			}
		);

		$obj = $this->AdminFactory( $users );

		$this->expectOutputRegex(
			'#^<select id="reference_user" name="msls\[reference_user\]">.*</select><p class="description">The user list has been limited to 100 users\.</p>$#s'
		);
		$obj->reference_user();
	}

	public function test_reference_user_over_max_singular(): void {
		$users = array(
			(object) array(
				'ID'            => 1,
				'user_nicename' => 'user-1',
			),
			(object) array(
				'ID'            => 2,
				'user_nicename' => 'user-2',
			),
		);

		Functions\when( 'apply_filters' )->alias(
			function ( string $hook, $value ) {
				return 'msls_max_reference_users_count' === $hook ? 1 : $value;
			}
		);

		Functions\expect( 'wp_list_pluck' )->once()->andReturnUsing(
			function ( array $list ): array {
				$this->assertCount( 1, $list );

				return array_column( $list, 'user_nicename', 'ID' );
			}
		);

		$obj = $this->AdminFactory( $users );

		$this->expectOutputRegex(
			'#^<select id="reference_user" name="msls\[reference_user\]">.*</select><p class="description">The user list has been limited to 1 user\.</p>$#s'
		);
		$obj->reference_user();
	}

	/**
	 * Every one of these renders a single settings field and writes nothing else.
	 *
	 * @return array<string, array{string, string}>
	 */
	public static function settings_field_provider(): array {
		return array(
			'activate_autocomplete' => array(
				'activate_autocomplete',
				'<input type="checkbox" id="activate_autocomplete" name="msls[activate_autocomplete]" value="1" /> <label for="activate_autocomplete">Activate experimental autocomplete inputs</label>',
			),
			'sort_by_description'   => array(
				'sort_by_description',
				'<input type="checkbox" id="sort_by_description" name="msls[sort_by_description]" value="1" /> <label for="sort_by_description">Sort languages by description</label>',
			),
			'exclude_current_blog'  => array(
				'exclude_current_blog',
				'<input type="checkbox" id="exclude_current_blog" name="msls[exclude_current_blog]" value="1" /> <label for="exclude_current_blog">Exclude this blog from output</label>',
			),
			'only_with_translation' => array(
				'only_with_translation',
				'<input type="checkbox" id="only_with_translation" name="msls[only_with_translation]" value="1" /> <label for="only_with_translation">Show only links with a translation</label>',
			),
			'output_current_blog'   => array(
				'output_current_blog',
				'<input type="checkbox" id="output_current_blog" name="msls[output_current_blog]" value="1" /> <label for="output_current_blog">Display link to the current language</label>',
			),
			'description'           => array(
				'description',
				'<input type="text" class="regular-text" id="description" name="msls[description]" value="" size="40"/>',
			),
			'before_output'         => array(
				'before_output',
				'<input type="text" class="regular-text" id="before_output" name="msls[before_output]" value="" size="30"/>',
			),
			'after_output'          => array(
				'after_output',
				'<input type="text" class="regular-text" id="after_output" name="msls[after_output]" value="" size="30"/>',
			),
			'before_item'           => array(
				'before_item',
				'<input type="text" class="regular-text" id="before_item" name="msls[before_item]" value="" size="30"/>',
			),
			'after_item'            => array(
				'after_item',
				'<input type="text" class="regular-text" id="after_item" name="msls[after_item]" value="" size="30"/>',
			),
			'content_filter'        => array(
				'content_filter',
				'<input type="checkbox" id="content_filter" name="msls[content_filter]" value="1" /> <label for="content_filter">Add hint for available translations</label>',
			),
		);
	}

	#[DataProvider( 'settings_field_provider' )]
	public function test_settings_field_renders( string $method, string $expected ): void {
		$obj = $this->AdminFactory();

		$this->expectOutputString( $expected );
		$obj->{$method}();
	}


	function test_rewrite_tizio(): void {
		$obj = $this->AdminFactory();

		$post_type          = \Mockery::mock( \WP_Post_Type::class );
		$post_type->rewrite = false;

		Functions\when( 'get_post_type_object' )->justReturn( $post_type );

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="rewrite_tizio" name="msls[rewrite_tizio]" value="" size="30" readonly="readonly"/>'
		);
		$obj->rewrite_tizio( 'tizio' );
	}

	function test_rewrite_pinko(): void {
		$obj = $this->AdminFactory();

		$post_type          = \Mockery::mock( \WP_Post_Type::class );
		$post_type->rewrite = true; // this should not be possible

		Functions\when( 'get_post_type_object' )->justReturn( $post_type );

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="rewrite_pinko" name="msls[rewrite_pinko]" value="" size="30" readonly="readonly"/>'
		);
		$obj->rewrite_pinko( 'pinko' );
	}

	function test_rewrite_pallino(): void {
		$obj = $this->AdminFactory();

		$post_type          = \Mockery::mock( \WP_Post_Type::class );
		$post_type->rewrite = array( 'slug' => 'pallino_slug' );

		Functions\when( 'get_post_type_object' )->justReturn( $post_type );

		$this->expectOutputString(
			'<input type="text" class="regular-text" id="rewrite_pallino" name="msls[rewrite_pallino]" value="pallino_slug" size="30" readonly="readonly"/>'
		);
		$obj->rewrite_pallino( 'pallino' );
	}

	function test_content_priority(): void {
		$obj = $this->AdminFactory();

		$this->expectOutputRegex( '/^<select id="content_priority" name="msls\[content_priority\]">.*$/' );
		$obj->content_priority();
	}

	function test_validate(): void {
		$obj = $this->AdminFactory();

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
		$obj = $this->AdminFactory();

		$arr = array(
			'abc'           => true,
			'blog_language' => 'it_IT',
		);
		$this->assertEquals( array( 'abc' => true ), $obj->set_blog_language( $arr ) );
	}

	function test_render(): void {
		$obj = $this->AdminFactory();

		Functions\expect( 'settings_fields' )->once();
		Functions\expect( 'do_settings_sections' )->once();

		$this->expectOutputRegex(
			'/^<div class="wrap"><div class="icon32" id="icon-options-general"><br\/><\/div><h1>Multisite Language Switcher Options<\/h1>.*$/'
		);
		$obj->render();
	}

	function test_language_section(): void {
		$obj = $this->AdminFactory();

		Functions\when( 'add_settings_field' )->returnArg();

		$this->assertEquals( 1, $obj->language_section() );
	}

	function test_main_section(): void {
		$obj = $this->AdminFactory();

		Functions\expect( 'add_settings_field' )->times( 12 )->andReturnFirstArg();

		$this->assertEquals( 12, $obj->main_section() );
	}

	function test_advanced_section(): void {
		$obj = $this->AdminFactory();

		Functions\expect( 'add_settings_field' )->times( 6 )->andReturnFirstArg();

		$this->assertEquals( 6, $obj->advanced_section() );
	}

	function test_rewrites_section(): void {
		$obj = $this->AdminFactory();

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

		$obj = $this->AdminFactory();

		$this->expectNotToPerformAssertions();
		$obj->register();
	}
}
