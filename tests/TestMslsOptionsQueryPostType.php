<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsQueryPostType;

/**
 * TestMslsOptionsQueryPostType
 */
class TestMslsOptionsQueryPostType extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( [ 'de_DE' => 42 ] );

		$this->test = new MslsOptionsQueryPostType();
	}

	public function test_has_value(): void {
		$this->assertIsBool( $this->test->has_value( 'de_DE' ) );
	}

	public function test_get_current_link(): void {
		Functions\expect( 'get_post_type_archive_link' )->once()->andReturn( 'https://example.org/queried-posttype' );

		$this->assertEquals( 'https://example.org/queried-posttype', $this->test->get_current_link() );
	}

}
