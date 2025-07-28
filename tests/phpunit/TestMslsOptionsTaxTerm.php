<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsOptionsTaxTerm;

final class TestMslsOptionsTaxTerm extends MslsUnitTestCase {

	private function MslsOptionsTaxTermFactory( $get_option_exec_times = 2 ): MslsOptionsTaxTerm {
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

		return new MslsOptionsTaxTerm( 42 );
	}

	public function test_get_postlink_empty(): void {
		$test = $this->MslsOptionsTaxTermFactory( 1 );

		$this->assertEquals( '', $test->get_postlink( '' ) );
	}

	public function test_check_url_empty(): void {
		$options = \Mockery::mock( MslsOptionsTaxTerm::class );

		$test = $this->MslsOptionsTaxTermFactory( 1 );

		$this->assertEquals( '', $test->check_base( null, $options ) );
		$this->assertEquals( '', $test->check_base( '', $options ) );
		$this->assertEquals( '', $test->check_base( false, $options ) );
	}

	public function test_check_url(): void {
		global $wp_rewrite;

		$wp_rewrite = \Mockery::mock( 'WP_Rewrite' );
		$wp_rewrite->shouldReceive( 'get_extra_permastruct' )->andReturn( '/schlagwort/' );

		$options = \Mockery::mock( MslsOptionsTaxTerm::class );
		$options->shouldReceive( 'get_tax_query' )->andReturn( '' );

		$expected = 'https://example.de/tag/keyword';

		$test = $this->MslsOptionsTaxTermFactory();

		$this->assertEquals( $expected, $test->check_base( 'https://example.de/schlagwort/keyword', $options ) );
	}

	public function test_check_url_permastruct_false(): void {
		global $wp_rewrite;

		$wp_rewrite = \Mockery::mock( 'WP_Rewrite' );
		$wp_rewrite->shouldReceive( 'get_extra_permastruct' )->andReturn( false );

		$options = \Mockery::mock( MslsOptionsTaxTerm::class );
		$options->shouldReceive( 'get_tax_query' )->andReturn( '' );

		$expected = 'https://example.de/schlagwort/keyword';

		$test = $this->MslsOptionsTaxTermFactory();

		$this->assertEquals( $expected, $test->check_base( $expected, $options ) );
	}

	public function test_get_option_name(): void {
		$test = $this->MslsOptionsTaxTermFactory( 1 );

		$this->assertSame( 'msls_term_42', $test->get_option_name() );
	}
}
