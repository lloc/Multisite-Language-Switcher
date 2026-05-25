<?php declare( strict_types=1 );

namespace lloc\MslsTests\Admin;

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use lloc\Msls\Admin\Bar;
use lloc\Msls\Admin\Icon;
use lloc\Msls\Blog\Blog;
use lloc\Msls\Blog\Collection;
use lloc\Msls\Options\Options;
use lloc\MslsTests\MslsUnitTestCase;

final class TestBar extends MslsUnitTestCase {

	private function BarFactory(): Bar {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_icon_type' )->andReturn( 'label' );

		$blog_a              = \Mockery::mock( Blog::class );
		$blog_a->userblog_id = 1;
		$blog_a->shouldReceive( 'get_title' )->andReturn( 'Blog A' );
		$blog_a->shouldReceive( 'get_blavatar' )->andReturn( '<div>Blavatar</div>' );

		$blog_b              = \Mockery::mock( Blog::class );
		$blog_b->userblog_id = 2;
		$blog_b->shouldReceive( 'get_title' )->andReturn( 'Blog B' );
		$blog_b->shouldReceive( 'get_blavatar' )->andReturn( '<div>Blavatar</div>' );

		$blog_c = null;

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( $blog_a );
		$collection->shouldReceive( 'get_plugin_active_blogs' )->andReturn( array( $blog_a, $blog_b, $blog_c ) );

		return new Bar( $options, $collection );
	}

	public function test_init(): void {
		$options = \Mockery::mock( Options::class );
		$options->shouldReceive( 'get_icon_type' )->andReturn( Icon::TYPE_LABEL );

		$collection = \Mockery::mock( Collection::class );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'is_admin_bar_showing' )->once()->andReturn( true );

		Actions\expectAdded( 'admin_bar_menu' )->once();

		$this->expectNotToPerformAssertions();
		Bar::init();
	}

	public function test_add_node_false(): void {
		$wp_admin_bar = \Mockery::mock( \WP_Admin_Bar::class );
		$wp_admin_bar->shouldReceive( 'get_node' )->once()->andReturnNull();

		$test = $this->BarFactory();

		$this->assertFalse( $test->add_node( $wp_admin_bar, 'node-id', 'title' ) );
	}

	public function test_add_node_true(): void {
		$wp_admin_bar = \Mockery::mock( \WP_Admin_Bar::class );
		$wp_admin_bar->shouldReceive( 'get_node' )->once()->andReturn( (object) array() );
		$wp_admin_bar->shouldReceive( 'add_node' )->once();

		$test = $this->BarFactory();

		$this->assertTrue( $test->add_node( $wp_admin_bar, 'node-id', 'title' ) );
	}

	public function test_update_admin_bar(): void {
		$wp_admin_bar = \Mockery::mock( \WP_Admin_Bar::class );
		$wp_admin_bar->shouldReceive( 'get_node' )->times( 3 )->andReturn( (object) array() );
		$wp_admin_bar->shouldReceive( 'add_node' )->times( 3 );

		$this->expectOutputString( '' );

		$this->BarFactory()->update_admin_bar( $wp_admin_bar );
	}
}
