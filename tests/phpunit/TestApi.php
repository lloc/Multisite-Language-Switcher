<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState( false )]
final class TestApi extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		require_once __DIR__ . '/../../includes/api.php';
	}

	public function test_msls_get_switcher_without_arguments(): void {
		Functions\expect( 'apply_filters' )->once()->with( 'msls_get_output', null )->andReturn( null );

		$this->assertSame( '', msls_get_switcher() );
	}

	public function test_msls_get_switcher_with_array(): void {
		$attr = array( 'before_item' => '<li>' );

		$output = \Mockery::mock();
		$output->shouldReceive( 'set_tags' )->once()->with( $attr )->andReturn( 'switcher' );

		Functions\expect( 'apply_filters' )->once()->with( 'msls_get_output', null )->andReturn( $output );

		$this->assertSame( 'switcher', msls_get_switcher( $attr ) );
	}

	public function test_msls_get_switcher_coerces_non_array_to_empty_array(): void {
		$output = \Mockery::mock();
		$output->shouldReceive( 'set_tags' )->once()->with( array() )->andReturn( 'switcher' );

		Functions\expect( 'apply_filters' )->once()->with( 'msls_get_output', null )->andReturn( $output );

		// The [sc_msls] shortcode passes '' when used without attributes.
		$this->assertSame( 'switcher', msls_get_switcher( '' ) );
	}
}
