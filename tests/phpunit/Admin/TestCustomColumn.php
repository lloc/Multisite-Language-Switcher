<?php declare( strict_types=1 );

namespace lloc\MslsTests\Admin;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Admin\CustomColumn;
use lloc\Msls\ContentTypes\PostType;
use lloc\Msls\Blog\Blog;
use lloc\Msls\Blog\Collection;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;

final class TestCustomColumn extends MslsUnitTestCase {

	private function CustomColumnFactory(): CustomColumn {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_icon_type' )->andReturn( 'flag' );

		$locales = array(
			'de_DE' => 'de',
			'en_US' => 'en',
		);

		foreach ( $locales as $locale => $alpha2 ) {
			$blog = \Mockery::mock( Blog::class );
			$blog->shouldReceive( 'get_alpha2' )->andReturn( $alpha2 );
			$blog->shouldReceive( 'get_language' )->andReturn( $locale );

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );
		$collection->shouldReceive( 'get' )->andReturn( $blogs );
		$collection->shouldReceive( 'get_current_blog_id' )->andReturn( 1 );

		return new CustomColumn( $options, $collection );
	}

	public function test_add_hooks_excluded(): void {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( true );

		$collection = \Mockery::mock( Collection::class );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$this->expectNotToPerformAssertions();
		CustomColumn::init();
	}

	public function test_add_hooks(): void {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );

		$collection = \Mockery::mock( Collection::class );

		$post_type = \Mockery::mock( PostType::class );
		$post_type->shouldReceive( 'get_request' )->andReturn( 'post' );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'msls_post_type' )->once()->andReturn( $post_type );

		Filters\expectAdded( 'manage_post_posts_columns' )->once();
		Actions\expectAdded( 'manage_post_posts_custom_column' )->once();
		Actions\expectAdded( 'trashed_post' )->once();

		$this->expectNotToPerformAssertions();

		CustomColumn::init();
	}

	public function test_th(): void {
		Functions\expect( 'add_query_arg' )->twice()->andReturn( 'https://msls.co/added-args' );
		Functions\expect( 'get_the_ID' )->twice()->andReturnValues( array( 1, 2 ) );
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 3 ) . '/' );

		$expected = array( 'mslscol' => '<span class="msls-icon-wrapper flag"><span class="flag-icon flag-icon-de">de_DE</span></span><span class="msls-icon-wrapper flag"><span class="flag-icon flag-icon-us">en_US</span></span>' );

		$test = $this->CustomColumnFactory();

		$this->assertEquals( $expected, $test->th( array() ) );
	}

	public function test_th_empty(): void {
		$options = \Mockery::mock( Options::class );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( array() );

		$obj = new CustomColumn( $options, $collection );

		$this->assertEmpty( $obj->th( array() ) );
	}

	public function test_td(): void {
		$item_id = 42;

		$post_type = \Mockery::mock( PostType::class );
		$post_type->shouldReceive( 'is_taxonomy' )->times( 3 )->andReturnFalse();
		$post_type->shouldReceive( 'get_request' )->twice()->andReturn( 'post' );

		Functions\expect( 'get_current_blog_id' )->twice()->andReturn( 13 );
		Functions\expect( 'get_blog_option' )->once()->andReturn( 'de_DE' );
		Functions\expect( 'is_admin' )->once()->andReturnTrue();
		Functions\expect( 'msls_content_types' )->times( 3 )->andReturn( $post_type );
		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 17 ) );
		Functions\expect( 'switch_to_blog' )->twice();
		Functions\expect( 'restore_current_blog' )->twice();
		Functions\expect( 'get_edit_post_link' )->once()->andReturn( 'edit-post-link' );
		Functions\expect( 'add_query_arg' )->once()->andReturn( 'added-query-args' );
		Functions\expect( 'get_admin_url' )->once()->andReturn( 'admin-url' );
		Functions\expect( 'msls_options' )->andReturn( \Mockery::mock( Options::class ) );

		$output = '<span class="msls-icon-wrapper flag"><a title="Edit the translation in the de_DE-blog" href="edit-post-link"><span class="dashicons dashicons-edit"></span></a>&nbsp;</span><span class="msls-icon-wrapper flag"><a title="Create a new translation in the en_US-blog" href="admin-url"><span class="dashicons dashicons-plus"></span></a>&nbsp;</span>';

		$this->expectOutputString( $output );

		$test = $this->CustomColumnFactory();

		$test->td( 'mslscol', $item_id );
	}
}
