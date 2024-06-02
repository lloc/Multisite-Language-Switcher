<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsAdminBar;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsLink;
use lloc\Msls\MslsOptions;

class TestMslsAdminBar extends MslsUnitTestCase {


	public function setUp(): void {
		parent::setUp();

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_icon_type' )->andReturn( 'label' );

		$blog_a              = \Mockery::mock( MslsBlog::class );
		$blog_a->userblog_id = 1;
		$blog_a->shouldReceive( 'get_title' )->andReturn( 'Blog A' );
		$blog_a->shouldReceive( 'get_blavatar' )->andReturn( '<div>Blavatar</div>' );

		$blog_b              = \Mockery::mock( MslsBlog::class );
		$blog_b->userblog_id = 2;
		$blog_b->shouldReceive( 'get_title' )->andReturn( 'Blog B' );
		$blog_b->shouldReceive( 'get_blavatar' )->andReturn( '<div>Blavatar</div>' );

		$blog_c = null;

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( $blog_a );
		$collection->shouldReceive( 'get_plugin_active_blogs' )->andReturn( array( $blog_a, $blog_b, $blog_c ) );

		$this->test = new MslsAdminBar( $options, $collection );
	}

	public function test_add_node_false(): void {
		$wp_admin_bar = \Mockery::mock( \WP_Admin_Bar::class );
		$wp_admin_bar->shouldReceive( 'get_node' )->once()->andReturnNull();

		$this->assertFalse( $this->test->add_node( $wp_admin_bar, 'node-id', 'title' ) );
	}

	public function test_add_node_true(): void {
		$wp_admin_bar = \Mockery::mock( \WP_Admin_Bar::class );
		$wp_admin_bar->shouldReceive( 'get_node' )->once()->andReturn( (object) array() );
		$wp_admin_bar->shouldReceive( 'add_node' )->once();

		$this->assertTrue( $this->test->add_node( $wp_admin_bar, 'node-id', 'title' ) );
	}

	public function test_update_admin_bar(): void {
		$wp_admin_bar = \Mockery::mock( \WP_Admin_Bar::class );
		$wp_admin_bar->shouldReceive( 'get_node' )->times( 3 )->andReturn( (object) array() );
		$wp_admin_bar->shouldReceive( 'add_node' )->times( 3 );

		$this->expectOutputString( '' );
		$this->test->update_admin_bar( $wp_admin_bar );
	}
}
