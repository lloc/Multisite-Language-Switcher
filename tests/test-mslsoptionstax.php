<?php

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsTax;

/**
 * WP_Test_MslsOptionsTax
 */
class WP_Test_MslsOptionsTax extends Msls_UnitTestCase {

	public function setUp(): void {
		parent::setUp();

		Functions\when( 'get_option' )->justReturn( [ 'de_DE' => 42 ] );
	}

	public function test_get_tax_query(): void {
		$obj = new MslsOptionsTax( 1 );

		$this->assertEquals( '', $obj->get_tax_query() );
	}

	public function test_get_postlink_method(): void {
		$obj = new MslsOptionsTax( 1 );

		$this->assertEquals( '', $obj->get_postlink( 'de_DE' ) );
	}

	public function current_link_data_provider(): array {
		return [ [ '', 0 ], [ '', 42 ] ];
	}

	/**
	 * @dataProvider current_link_data_provider
	 */
	public function test_get_current_link( string $expected, int $object_id ): void {
		$obj = new MslsOptionsTax( $object_id );

		$this->assertEquals( $expected, $obj->get_current_link() );
	}

}
