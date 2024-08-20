<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsCustomColumn;
use lloc\Msls\MslsOptions;

class TestMslsCustomColumn extends MslsUnitTestCase {

	public function test_th(): void {
		Functions\expect( 'add_query_arg' )->twice()->andReturn( 'https://msls.co/added-args' );
		Functions\expect( 'get_the_ID' )->twice()->andReturnValues( array( 1, 2 ) );
		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 2 ) . '/' );

		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'get_icon_type' )->andReturn( 'flag' );

		foreach ( array(
			'de_DE' => 'de',
			'en_US' => 'en',
		) as $locale => $alpha2 ) {
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive(
				array(
					'get_alpha2'   => $alpha2,
					'get_language' => $locale,
				)
			);

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );
		$collection->shouldReceive( 'get' )->andReturn( $blogs );
		$collection->shouldReceive( 'get_current_blog_id' )->andReturn( 1 );

		$obj      = new MslsCustomColumn( $options, $collection );
		$expected = array( 'mslscol' => '<span class="msls-icon-wrapper flag"><span class="flag-icon flag-icon-de">de_DE</span></span><span class="msls-icon-wrapper flag"><span class="flag-icon flag-icon-us">en_US</span></span>' );

		$this->assertEquals( $expected, $obj->th( array() ) );
	}

	public function test_th_empty(): void {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->once()->andReturn( array() );

		$obj = new MslsCustomColumn( $options, $collection );

		$this->assertEmpty( $obj->th( array() ) );
	}
}
