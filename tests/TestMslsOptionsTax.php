<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;

use lloc\Msls\MslsOptionsTax;

/**
 * TestMslsOptionsTax
 */
class TestMslsOptionsTax extends MslsUnitTestCase {

	protected $woo;

	protected function setUp(): void {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->andReturn( array( 'de_DE' => 42 ) );

		$this->test = new MslsOptionsTax( 0 );
	}

	public function test_get_tax_query(): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$this->assertEquals( '', $this->test->get_tax_query() );
	}

	public function test_get_tax_query_woo(): void {
		global $wp_query;

		$expected = 'taxonomy_query_string_1';
		$wp_query = (object) array(
			'tax_query' => (object) array(
				'queries' => array(
					1 => array( 'taxonomy' => $expected ),
				),
			),
		);

		Functions\expect( 'is_woocommerce' )->once()->andReturn( true );

		$this->assertEquals( $expected, $this->test->get_tax_query( array() ) );
	}

	public function test_get_tax_query_set(): void {
		global $wp_query;

		$expected = 'taxonomy_query_string_0';
		$wp_query = (object) array(
			'tax_query' => (object) array(
				'queries' => array(
					0 => array( 'taxonomy' => $expected ),
				),
			),
		);

		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$this->assertEquals( $expected, $this->test->get_tax_query( array() ) );
	}

	public function test_get_postlink(): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$this->assertEquals( '', $this->test->get_postlink( 'de_DE' ) );
	}

	public function test_get_postlink_empty(): void {
		Functions\expect( 'is_woocommerce' )->never();

		$this->assertEquals( '', $this->test->get_postlink( 'it_IT' ) );
	}

	public function test_get_current_link(): void {
		$this->assertIsString( $this->test->get_current_link() );
	}

	public function test_get_term_link(): void {
		global $wp_query;

		$wp_query = (object) array(
			'tax_query' => (object) array(
				'queries' => array(
					0 => array( 'taxonomy' => 'taxonomy_query_string_0' ),
				),
			),
		);

		$expected = 'http://example.com/term_link';

		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );
		Functions\expect( 'get_term_link' )->once()->andReturn( $expected );

		$this->assertEquals( $expected, $this->test->get_term_link( 42 ) );
	}

	public function test_get_term_link_wp_error(): void {
		global $wp_query;

		$wp_query = (object) array(
			'tax_query' => (object) array(
				'queries' => array(
					0 => array( 'taxonomy' => 'taxonomy_query_string_0' ),
				),
			),
		);

		$wp_error = \Mockery::mock( 'WP_Error' );

		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );
		Functions\expect( 'get_term_link' )->once()->andReturn( $wp_error );

		$this->assertEquals( '', $this->test->get_term_link( 42 ) );
	}

	public function test_get_term_link_empty(): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$this->assertEquals( '', $this->test->get_term_link( 42 ) );
	}
}
