<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsOptionsTaxTerm;

class TestMslsOptionsTaxTerm extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		Functions\expect( 'get_option' )->once()->with( 'msls_term_42' )->andReturn( array( 'de_DE' => 42 ) );

		$this->test = new MslsOptionsTaxTerm( 42 );
	}
	public function test_get_postlink_empty(): void {
		$this->assertEquals( '', $this->test->get_postlink( '' ) );
	}

	public function test_check_url_empty(): void {
		$options = \Mockery::mock( MslsOptionsTaxTerm::class );

		$this->assertEquals( '', $this->test->check_base( null, $options ) );
		$this->assertEquals( '', $this->test->check_base( '', $options ) );
		$this->assertEquals( '', $this->test->check_base( false, $options ) );
	}

	public function test_check_url(): void {
		global $wp_rewrite;

		$wp_rewrite = \Mockery::mock( 'WP_Rewrite' );
		$wp_rewrite->shouldReceive( 'get_extra_permastruct' )->andReturn( '/schlagwort/' );

		$options = \Mockery::mock( MslsOptionsTaxTerm::class );
		$options->shouldReceive( 'get_tax_query' )->andReturn( '' );

		$expected = 'https://example.de/tag/keyword';
		$this->assertEquals( $expected, $this->test->check_base( 'https://example.de/schlagwort/keyword', $options ) );
	}

	public function test_check_url_permastruct_false(): void {
		global $wp_rewrite;

		$wp_rewrite = \Mockery::mock( 'WP_Rewrite' );
		$wp_rewrite->shouldReceive( 'get_extra_permastruct' )->andReturn( false );

		$options = \Mockery::mock( MslsOptionsTaxTerm::class );
		$options->shouldReceive( 'get_tax_query' )->andReturn( '' );

		$expected = 'https://example.de/schlagwort/keyword';
		$this->assertEquals( $expected, $this->test->check_base( $expected, $options ) );
	}

	public function test_get_option_name() {
		$this->assertSame( 'msls_term_42', $this->test->get_option_name() );
	}
}
