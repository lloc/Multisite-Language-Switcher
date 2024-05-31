<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsFields;
use lloc\Msls\MslsRequest;

class TestMslsRequest extends MslsUnitTestCase {

	public function test_isset_ok(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, MslsFields::FIELD_MSLS_ID )->andReturn( 13 );
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, MslsFields::FIELD_MSLS_FILTER )->andReturn( 17 );

		$this->assertTrue( MslsRequest::isset( array( MslsFields::FIELD_MSLS_FILTER, MslsFields::FIELD_MSLS_ID ) ) );
	}

	public function test_has_var_ok(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, MslsFields::FIELD_MSLS_ID )->andReturn( 13 );

		$this->assertTrue( MslsRequest::has_var( MslsFields::FIELD_MSLS_ID ) );
	}

	public function test_get_var_ok(): void {
		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, MslsFields::FIELD_MSLS_FILTER, FILTER_SANITIZE_NUMBER_INT )->andReturn( 17 );

		$this->assertEquals( 17, MslsRequest::get_var( MslsFields::FIELD_MSLS_FILTER ) );
	}

	public function test_isset_ko(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, MslsFields::FIELD_MSLS_FILTER )->andReturn( 17 );

		$this->assertFalse( MslsRequest::isset( array( MslsFields::FIELD_MSLS_FILTER, 'non_existent_key' ) ) );
	}

	public function test_has_var_ko(): void {
		$this->assertFalse( MslsRequest::has_var( 'non_existent_key' ) );
	}

	public function test_get_var_ko(): void {
		$this->assertNull( MslsRequest::get_var( 'non_existent_key' ) );
	}

	public function test_request_ok(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_POST, MslsFields::FIELD_POST_TYPE )->andReturn( false );
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, MslsFields::FIELD_POST_TYPE )->andReturn( true );

		Functions\expect( 'filter_input' )->once()->with( INPUT_GET, MslsFields::FIELD_POST_TYPE, FILTER_SANITIZE_FULL_SPECIAL_CHARS )->andReturn( 'book' );

		Functions\expect( 'filter_has_var' )->once()->with( INPUT_POST, MslsFields::FIELD_TAXONOMY )->andReturn( true );
		Functions\expect( 'filter_input' )->once()->with( INPUT_POST, MslsFields::FIELD_TAXONOMY, FILTER_SANITIZE_FULL_SPECIAL_CHARS )->andReturn( 'fantasy' );

		$expected = array(
			'post_type' => 'book',
			'taxonomy'  => 'fantasy',
		);

		$this->assertEquals( $expected, MslsRequest::get_request( array( MslsFields::FIELD_POST_TYPE, MslsFields::FIELD_TAXONOMY ) ) );
	}

	public function test_request_ko(): void {
		Functions\expect( 'filter_has_var' )->times( 4 )->andReturn( false );

		$expected = array(
			'post_type' => '',
			'taxonomy'  => '',
		);

		$this->assertEquals( $expected, MslsRequest::get_request( array( MslsFields::FIELD_POST_TYPE, MslsFields::FIELD_TAXONOMY ) ) );
	}
}
