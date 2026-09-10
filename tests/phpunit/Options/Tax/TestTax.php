<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options\Tax;

use Brain\Monkey\Functions;
use lloc\Msls\Options\Tax\Category;
use lloc\Msls\Options\Tax\Tax;
use lloc\Msls\Options\Tax\Term;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestTax extends MslsUnitTestCase {

	private function OptionsTaxFactory(): Tax {
		Functions\expect( 'get_option' )->atLeast()->once()->andReturn( array( 'de_DE' => 42 ) );

		return new Tax();
	}

	public function test_create_category(): void {
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'is_admin' )->once()->andReturnFalse();
		Functions\expect( 'is_category' )->once()->with( 42 )->andReturnTrue();
		Functions\expect( 'get_option' )->atLeast()->once()->andReturn( array( 'de_DE' => 42 ) );

		$this->assertInstanceOf( Category::class, Tax::create() );
	}

	public function test_create_post_tag(): void {
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 42 );
		Functions\expect( 'is_admin' )->once()->andReturnFalse();
		Functions\expect( 'is_category' )->once()->andReturnFalse();
		Functions\expect( 'is_tag' )->once()->andReturnTrue();
		Functions\expect( 'get_option' )->atLeast()->once()->andReturn( array( 'de_DE' => 42 ) );

		$this->assertInstanceOf( Term::class, Tax::create() );
	}

	private function TaxWithTranslatedTermFactory(): Tax {
		Functions\expect( 'get_option' )->once()->with( 'msls_term_42' )->andReturn( array( 'de_DE' => 7 ) );

		return new Tax( 42 );
	}

	private function expect_tax_query( string $taxonomy ): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$GLOBALS['wp_query']                     = new \stdClass();
		$GLOBALS['wp_query']->tax_query          = new \stdClass();
		$GLOBALS['wp_query']->tax_query->queries = array( array( 'taxonomy' => $taxonomy ) );
	}

	public function test_get_max_pages_from_the_term_count(): void {
		$test = $this->TaxWithTranslatedTermFactory();

		$this->expect_tax_query( 'category' );

		$term        = \Mockery::mock( '\WP_Term' );
		$term->count = 25;

		Functions\expect( 'get_term' )->once()->with( 7, 'category' )->andReturn( $term );
		Functions\expect( 'get_option' )->once()->with( 'posts_per_page', 10 )->andReturn( 10 );

		$this->assertEquals( 3, $test->get_max_pages( 'de_DE', Tax::PAGINATION_ARCHIVE ) );

		unset( $GLOBALS['wp_query'] );
	}

	public function test_get_max_pages_falls_back_to_the_queried_term(): void {
		$test = $this->TaxWithTranslatedTermFactory();

		$this->expect_tax_query( 'category' );

		$term        = \Mockery::mock( '\WP_Term' );
		$term->count = 8;

		Functions\expect( 'get_term' )->once()->with( 42, 'category' )->andReturn( $term );
		Functions\expect( 'get_option' )->once()->with( 'posts_per_page', 10 )->andReturn( 10 );

		$this->assertEquals( 1, $test->get_max_pages( 'es_ES', Tax::PAGINATION_ARCHIVE ) );

		unset( $GLOBALS['wp_query'] );
	}

	public function test_get_max_pages_without_a_taxonomy(): void {
		$test = $this->TaxWithTranslatedTermFactory();

		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$this->assertEquals( 0, $test->get_max_pages( 'de_DE', Tax::PAGINATION_ARCHIVE ) );
	}

	public function test_get_max_pages_of_a_post_split_by_nextpage(): void {
		$test = $this->TaxWithTranslatedTermFactory();

		$this->assertEquals( 0, $test->get_max_pages( 'de_DE', Tax::PAGINATION_SINGLE ) );
	}

	public function test_get_tax_query(): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$test = $this->OptionsTaxFactory();

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

		$test = $this->OptionsTaxFactory();

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

		$test = $this->OptionsTaxFactory();

		$this->assertEquals( $expected, $test->get_tax_query( array() ) );
	}

	public function test_get_postlink(): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$test = $this->OptionsTaxFactory();

		$this->assertEquals( '', $test->get_postlink( 'de_DE' ) );
	}

	public function test_get_postlink_empty(): void {
		Functions\expect( 'is_woocommerce' )->never();

		$test = $this->OptionsTaxFactory();

		$this->assertEquals( '', $test->get_postlink( 'it_IT' ) );
	}

	public function test_get_current_link(): void {
		$test = $this->OptionsTaxFactory();

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

		$test = $this->OptionsTaxFactory();

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

		$test = $this->OptionsTaxFactory();

		$this->assertEquals( '', $test->get_term_link( 42 ) );
	}

	public function test_get_term_link_empty(): void {
		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );

		$this->assertEquals( '', $this->OptionsTaxFactory()->get_term_link( 42 ) );
	}

	public function test_get_permalink_returns_empty_when_no_translation(): void {
		$test = $this->OptionsTaxFactory();

		$this->assertSame( '', $test->get_permalink( 'fr_FR' ) );
	}

	public function test_get_permalink_returns_url_when_term_link_succeeds(): void {
		global $wp_query;

		$wp_query = (object) array(
			'tax_query' => (object) array(
				'queries' => array(
					0 => array( 'taxonomy' => 'category' ),
				),
			),
		);

		$expected = 'http://example.com/category/asia/';

		Functions\expect( 'is_woocommerce' )->once()->andReturn( false );
		Functions\expect( 'get_term_link' )->once()->andReturn( $expected );

		$test = $this->OptionsTaxFactory();

		$this->assertSame( $expected, $test->get_permalink( 'de_DE' ) );
	}

	public function test_get_base_option() {
		$this->assertEquals( '', Tax::get_base_option() );
	}
}
