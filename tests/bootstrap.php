<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

class Msls_UnitTestCase extends TestCase {

	/**
	 * Instance of the class to test
	 *
	 * @var object $test
	 */
	protected $test;

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( '__' )->returnArg();
	}

	/**
	 * Get a of the MslsBlogCollection class that contains some blogs
	 *
	 * @param $map
	 *
	 * @return void
	 */
	public function getBlogsCollection( $map = [ 'de_DE' => 'de', 'en_US' => 'en' ] ): MslsBlogCollection {
		foreach ( $map as $locale => $alpha2 ) {
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive( [
				'get_alpha2'   => $alpha2,
				'get_language' => $locale,
			] );

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( $blogs );
		$collection->shouldReceive( 'get' )->andReturn( $blogs );

		return $collection;
	}

	protected function tearDown(): void {
		restore_error_handler();

		Monkey\tearDown();
		parent::tearDown();
	}

}
