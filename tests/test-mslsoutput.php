<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsOutput, lloc\Msls\MslsOptions, lloc\Msls\MslsOptionsPost;

class WP_Test_MslsOutput extends Msls_UnitTestCase {

	/**
	 * Verify the static init-method
	 */
	function test_init_method() {
		$obj = MslsOutput::init();
		$this->assertInstanceOf( MslsOutput::class, $obj );
		return $obj;
	}

	/**
	 * Verify the get-method
	 * @depends test_init_method
	 */
	function test_get_method( $obj ) {
		$this->assertInternalType( 'array', $obj->get( 0 ) );
	}

	/**
	 * Verify the __toString-method
	 * @depends test_init_method
	 */
	function test___toString_method( $obj ) {
		$this->assertInternalType( 'string', $obj->__toString() );
		$this->assertInternalType( 'string', strval( $obj ) );
		$this->assertEquals( $obj->__toString(), strval( $obj ) );
	}

	/**
	 * Verify the get_tags-method
	 * @depends test_init_method
	 */
	function test_get_tags_method( $obj ) {
		$this->assertInternalType( 'array', $obj->get_tags() );
	}
	
	/**
	 * Verify the set_tags-method
	 * @depends test_init_method
	 */
	function test_set_tags_method( $obj ) {
		$this->assertInstanceOf( MslsOutput::class, $obj->set_tags() );
	}

	/**
	 * Verify the is_requirements_not_fulfilled-method
	 * @depends test_init_method
	 */
	function test_is_requirements_not_fulfilled_method_with_null( $obj ) {
		$test = $obj->is_requirements_not_fulfilled( null, false, 'de_DE' );
		$this->assertFalse( $test );

		$test = $obj->is_requirements_not_fulfilled( null, true, 'de_DE' );
		$this->assertTrue( $test );
	}

	/**
	 * Verify the is_requirements_not_fulfilled-method
	 * @depends test_init_method
	 */
	function test_is_requirements_not_fulfilled_method_with_mslsoptions( $obj ) {
		$mydata = new MslsOptions();

		$test = $obj->is_requirements_not_fulfilled( $mydata, false, 'de_DE' );
		$this->assertFalse( $test );

		$test = $obj->is_requirements_not_fulfilled( $mydata, true, 'de_DE' );
		$this->assertFalse( $test );
	}

	/**
	 * Verify the is_requirements_not_fulfilled-method
	 * @depends test_init_method
	 */
	function test_is_requirements_not_fulfilled_method_with_mslsoptionspost( $obj ) {
		$mydata = new MslsOptionsPost();

		$test = $obj->is_requirements_not_fulfilled( $mydata, false, 'de_DE' );
		$this->assertFalse( $test );

		$test = $obj->is_requirements_not_fulfilled( $mydata, true, 'de_DE' );
		$this->assertTrue( $test );
	}

}
