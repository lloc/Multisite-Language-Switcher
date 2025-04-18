<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;

final class TestMslsBlogCollection extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp(); // TODO: Change the autogenerated stub

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_order' )->andReturn( 'description' );
		$options->shouldReceive( 'is_excluded' )->andReturn( false );
		$options->shouldReceive( 'has_value' )->andReturn( false );

		Functions\expect( 'msls_options' )->atLeast()->once()->andReturn( $options );

		$a = \Mockery::mock( MslsBlog::class );
		$b = \Mockery::mock( MslsBlog::class );
		$c = \Mockery::mock( MslsBlog::class );

		$a->userblog_id = 1;
		$b->userblog_id = 2;
		$c->userblog_id = 3;

		Functions\expect( 'get_current_blog_id' )->atLeast()->once()->andReturn( 1 );
		Functions\expect( 'get_users' )->atLeast()->once()->andReturn( array() );
		Functions\expect( 'get_blogs_of_user' )->atLeast()->once()->andReturn( array( $a, $b, $c ) );

		Functions\expect( 'get_blog_option' )->atLeast()->once()->andReturnUsing(
			function ( $blog_id, $option ) {
				$wplang = array(
					1 => 'de_DE',
					2 => 'it_IT',
					3 => 'fr_FR',
				);

				$msls = array(
					1 => array( 'description' => 'Deutsch' ),
					2 => array( 'description' => 'Italiano' ),
					3 => array( 'description' => 'Français' ),
				);

				switch ( $option ) {
					case 'active_plugins':
						$value = in_array(
							$blog_id,
							array( 1, 2 )
						) ? array( 'multisite-language-switcher/MultisiteLanguageSwitcher.php' ) : array();
						break;
					case 'WPLANG':
						$value = $wplang[ $blog_id ] ?? false;
						break;
					case 'msls':
							$value = $msls[ $blog_id ] ?? false;
						break;
				}

				return $value;
			}
		);
	}

	public function test_get_configured_blog_description_empty(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertEquals( 'Test', $obj->get_configured_blog_description( 0, 'Test' ) );

		$this->assertEquals( 'Deutsch', $obj->get_configured_blog_description( 1 ) );
		$this->assertEquals( 'Italiano', $obj->get_configured_blog_description( 2 ) );
		$this->assertEquals( 'Français', $obj->get_configured_blog_description( 3 ) );

		$this->assertFalse( $obj->get_configured_blog_description( 4 ) );
	}

	public function test_get_blogs_of_reference_user(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'has_value' )->andReturn( true );

		$obj = new MslsBlogCollection();

		$this->assertIsArray( $obj->get_blogs_of_reference_user( $options ) );
	}

	public function test_get_current_blog_id(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertIsInt( $obj->get_current_blog_id() );
	}

	public function test_has_current_blog(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertIsBool( $obj->has_current_blog() );
	}

	public function test_is_current_blog_true(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$blog              = \Mockery::mock( MslsBlog::class );
		$blog->userblog_id = 1;

		$this->assertTrue( $obj->is_current_blog( $blog ) );
	}

	public function test_is_current_blog_false(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$blog              = \Mockery::mock( MslsBlog::class );
		$blog->userblog_id = 2;

		$this->assertFalse( $obj->is_current_blog( $blog ) );
	}

	public function test_get_objects(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertIsArray( $obj->get_objects() );
	}

	public function test_get_object(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertInstanceOf( MslsBlog::class, $obj->get_object( 1 ) );
		$this->assertNull( $obj->get_object( 4 ) );
	}

	public function test_is_plugin_active_networkwide(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn(
			array( 'multisite-language-switcher/MultisiteLanguageSwitcher.php' => 'Multisite Language Switcher' )
		);

		$obj = new MslsBlogCollection();

		$this->assertTrue( $obj->is_plugin_active( 4 ) );
	}

	public function test_is_plugin_active(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertTrue( $obj->is_plugin_active( 1 ) );
		$this->assertTrue( $obj->is_plugin_active( 2 ) );

		$this->assertFalse( $obj->is_plugin_active( 3 ) );
	}

	public function test_get_plugin_active_blogs(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertIsArray( $obj->get_plugin_active_blogs() );
	}

	public function test_get(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertIsArray( $obj->get() );
	}

	public function test_get_filtered(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertIsArray( $obj->get_filtered() );
		$this->assertIsArray( $obj->get_filtered( true ) );
	}

	public function test_get_users(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertIsArray( $obj->get_users() );
	}

	public function test_get_current_blog(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertInstanceOf( MslsBlog::class, $obj->get_current_blog() );
	}

	public function test_get_blog_language(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertEquals( 'de_DE', $obj->get_blog_language( 1 ) );
		$this->assertEquals( 'it_IT', $obj->get_blog_language( 2 ) );
		$this->assertEquals( 'fr_FR', $obj->get_blog_language( 3 ) );

		$this->assertEquals( 'de_DE', $obj->get_blog_language() );
	}

	public function test_get_blog_id(): void {
		Functions\expect( 'get_site_option' )->once()->andReturn( array() );

		$obj = new MslsBlogCollection();

		$this->assertEquals( 1, $obj->get_blog_id( 'de_DE' ) );
		$this->assertEquals( 2, $obj->get_blog_id( 'it_IT' ) );

		$this->assertNull( $obj->get_blog_id( 'fr_FR' ) );
	}
}
