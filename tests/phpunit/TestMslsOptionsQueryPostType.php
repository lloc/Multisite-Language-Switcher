<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsOptionsQueryPostType;
use lloc\Msls\MslsSqlCacher;

final class TestMslsOptionsQueryPostType extends MslsUnitTestCase {

	private function MslsOptionsQueryPostTypeFactory(): MslsOptionsQueryPostType {
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_query_var' )->once()->andReturn( 'queried-posttype' );

		$sql_cacher = \Mockery::mock( MslsSqlCacher::class );
		$sql_cacher->shouldReceive( 'prepare' )->never();
		$sql_cacher->shouldReceive( 'get_var' )->never();

		return new MslsOptionsQueryPostType( $sql_cacher );
	}

	public function test_has_value_existing(): void {
		Functions\expect( 'get_post_type_object' )->once()->andReturn(
			(object) array(
				'labels' => array(
					'name'          => 'Post Type Name',
					'singular_name' => 'Post Type Singular Name',
				),
			)
		);

		$test = $this->MslsOptionsQueryPostTypeFactory();

		$this->assertTrue( $test->has_value( 'de_DE' ) );
	}

	public function test_has_value_not_existing(): void {
		$post_type = \Mockery::mock( '\WP_Post_Type' );
		Functions\expect( 'get_post_type_object' )->once()->andReturn( $post_type );

		$test = $this->MslsOptionsQueryPostTypeFactory();

		$this->assertTrue( $test->has_value( 'it_IT' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_post_type_archive_link' )->once()->andReturn( 'https://msls.co/queried-posttype' );

		$test = $this->MslsOptionsQueryPostTypeFactory();

		$this->assertEquals( 'https://msls.co/queried-posttype', $test->get_current_link() );
	}
}
