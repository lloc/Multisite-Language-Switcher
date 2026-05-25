<?php declare( strict_types=1 );

namespace lloc\MslsTests\Request;

use Brain\Monkey\Functions;
use lloc\Msls\Request\Fields;
use lloc\Msls\RestApi\Request;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestMslsRequest extends MslsUnitTestCase {

	public function test_isset_ok(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, Fields::FIELD_MSLS_ID )->andReturn( true );
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, Fields::FIELD_MSLS_FILTER )->andReturn( true );

		$this->assertTrue( Request::isset( array( Fields::FIELD_MSLS_FILTER, Fields::FIELD_MSLS_ID ) ) );
	}

	public function test_has_var_ok(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, Fields::FIELD_MSLS_ID )->andReturn( true );

		$this->assertTrue( Request::has_var( Fields::FIELD_MSLS_ID ) );
	}

	public function test_get_var_null(): void {
		// INPUT_REQUEST (99) has been removed as of PHP 8.0.0; was not implemented previously.
		$this->assertNull( Request::get_var( Fields::FIELD_MSLS_FILTER, 99 ) );

		// INPUT_SESSION (6) has been removed as of PHP 8.0.0; was not implemented previously.
		$this->assertNull( Request::get_var( Fields::FIELD_MSLS_FILTER, 6 ) );
	}

	public function test_get_var_ok(): void {
		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, Fields::FIELD_MSLS_FILTER, FILTER_SANITIZE_NUMBER_INT )->andReturn( 17 );

		$this->assertEquals( 17, Request::get_var( Fields::FIELD_MSLS_FILTER ) );
	}

	public function test_isset_ko(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, Fields::FIELD_MSLS_FILTER )->andReturn( false );

		$this->assertFalse( Request::isset( array( Fields::FIELD_MSLS_FILTER, 'non_existent_key' ) ) );
	}

	public function test_has_var_ko(): void {
		$this->assertFalse( Request::has_var( 'non_existent_key' ) );
	}

	public function test_get_var_ko(): void {
		$this->assertNull( Request::get_var( 'non_existent_key' ) );
	}

	public function test_request_ok(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_POST, Fields::FIELD_POST_TYPE )->andReturn( false );
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, Fields::FIELD_POST_TYPE )->andReturn( true );

		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, Fields::FIELD_POST_TYPE, FILTER_SANITIZE_FULL_SPECIAL_CHARS )->andReturn( 'book' );

		Functions\expect( 'filter_has_var' )->once()->with( INPUT_POST, Fields::FIELD_TAXONOMY )->andReturn( true );
		Functions\expect( 'filter_input' )->once()->with( INPUT_POST, Fields::FIELD_TAXONOMY, FILTER_SANITIZE_FULL_SPECIAL_CHARS )->andReturn( 'fantasy' );

		$expected = array(
			'post_type' => 'book',
			'taxonomy'  => 'fantasy',
		);

		$this->assertEquals( $expected, Request::get_request( array( Fields::FIELD_POST_TYPE, Fields::FIELD_TAXONOMY ) ) );
	}

	public function test_request_ko(): void {
		Functions\expect( 'filter_has_var' )->times( 4 )->andReturn( false );

		$expected = array(
			'post_type' => '',
			'taxonomy'  => '',
		);

		$this->assertEquals( $expected, Request::get_request( array( Fields::FIELD_POST_TYPE, Fields::FIELD_TAXONOMY ) ) );
	}
}
