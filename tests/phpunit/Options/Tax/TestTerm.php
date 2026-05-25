<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options\Tax;

use Brain\Monkey\Functions;
use lloc\Msls\Options\Tax\Term;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestTerm extends MslsUnitTestCase {

	private function OptionsTaxTermFactory( $get_option_exec_times = 2 ): Term {
		Functions\expect( 'get_option' )->times( $get_option_exec_times )->andReturnUsing(
			function ( $value ) {
				if ( 'msls_term_42' === $value ) {
						return array( 'de_DE' => 42 );
				}

				if ( 'tag_base' === $value ) {
					return 'tag';
				}

				return null;
			}
		);

		return new Term( 42 );
	}

	public function test_get_postlink_empty(): void {
		$test = $this->OptionsTaxTermFactory( 1 );

		$this->assertEquals( '', $test->get_postlink( '' ) );
	}

	public function test_check_url_empty(): void {
		$options = \Mockery::mock( Term::class );

		$test = $this->OptionsTaxTermFactory( 1 );

		$this->assertEquals( '', $test->check_base( null, $options ) );
		$this->assertEquals( '', $test->check_base( '', $options ) );
		$this->assertEquals( '', $test->check_base( false, $options ) );
	}

	public function test_check_url(): void {
		global $wp_rewrite;

		$wp_rewrite = \Mockery::mock( 'WP_Rewrite' );
		$wp_rewrite->shouldReceive( 'get_extra_permastruct' )->andReturn( '/schlagwort/' );

		$options = \Mockery::mock( Term::class );
		$options->shouldReceive( 'get_tax_query' )->andReturn( '' );

		$expected = 'https://example.de/tag/keyword';

		$test = $this->OptionsTaxTermFactory();

		$this->assertEquals( $expected, $test->check_base( 'https://example.de/schlagwort/keyword', $options ) );
	}

	public function test_check_url_permastruct_false(): void {
		global $wp_rewrite;

		$wp_rewrite = \Mockery::mock( 'WP_Rewrite' );
		$wp_rewrite->shouldReceive( 'get_extra_permastruct' )->andReturn( false );

		$options = \Mockery::mock( Term::class );
		$options->shouldReceive( 'get_tax_query' )->andReturn( '' );

		$expected = 'https://example.de/schlagwort/keyword';

		$test = $this->OptionsTaxTermFactory();

		$this->assertEquals( $expected, $test->check_base( $expected, $options ) );
	}

	public function test_get_option_name(): void {
		$test = $this->OptionsTaxTermFactory( 1 );

		$this->assertSame( 'msls_term_42', $test->get_option_name() );
	}
}
