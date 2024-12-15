<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsOptionsTax;
use lloc\Msls\MslsOptionsTaxTerm;
use lloc\Msls\MslsOptionsTaxTermCategory;

final class TestMslsOptionsTax extends MslsUnitTestCase {

	private function MslsOptionsTaxFactory(): MslsOptionsTax {
		Functions\expect( 'get_option' )->atLeast()->once()->andReturn( array( 'de_DE' => 42 ) );

		return new MslsOptionsTax( 0 );
	}

	public function test_create_category(): void {
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'is_admin' )->once()->andReturnFalse();
		Functions\expect( 'is_category' )->once()->andReturnTrue();
		Functions\expect( 'get_option' )->atLeast()->once()->andReturn( array( 'de_DE' => 42 ) );

		$this->assertInstanceOf( MslsOptionsTaxTermCategory::class, MslsOptionsTax::create() );
	}

	public function test_create_post_tag(): void {
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'is_admin' )->once()->andReturnFalse();
		Functions\expect( 'is_category' )->once()->andReturnFalse();
		Functions\expect( 'is_tag' )->once()->andReturnTrue();
		Functions\expect( 'get_option' )->atLeast()->once()->andReturn( array( 'de_DE' => 42 ) );

		$this->assertInstanceOf( MslsOptionsTaxTerm::class, MslsOptionsTax::create() );
	}

	public function test_get_tax_query(): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$test = $this->MslsOptionsTaxFactory();

		$this->assertEquals( '', $test->get_tax_query() );
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

		$test = $this->MslsOptionsTaxFactory();

		$this->assertEquals( $expected, $test->get_tax_query() );
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

		$test = $this->MslsOptionsTaxFactory();

		$this->assertEquals( $expected, $test->get_tax_query( array() ) );
	}

	public function test_get_postlink(): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$test = $this->MslsOptionsTaxFactory();

		$this->assertEquals( '', $test->get_postlink( 'de_DE' ) );
	}

	public function test_get_postlink_empty(): void {
		Functions\expect( 'is_woocommerce' )->never();

		$test = $this->MslsOptionsTaxFactory();

		$this->assertEquals( '', $test->get_postlink( 'it_IT' ) );
	}

	public function test_get_current_link(): void {
		$test = $this->MslsOptionsTaxFactory();

		$this->assertIsString( $test->get_current_link() );
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

		$test = $this->MslsOptionsTaxFactory();

		$this->assertEquals( $expected, $test->get_term_link( 42 ) );
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

		$test = $this->MslsOptionsTaxFactory();

		$this->assertEquals( '', $test->get_term_link( 42 ) );
	}

	public function test_get_term_link_empty(): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$test = $this->MslsOptionsTaxFactory();

		$this->assertEquals( '', $test->get_term_link( 42 ) );
	}
}
