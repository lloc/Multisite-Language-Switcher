<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOutput;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsOptionsPost;
use Brain\Monkey\Functions;

class TestMslsOutput extends Msls_UnitTestCase {

	function get_test() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( [
			'has_current_blog' => true,
			'get_current_blog' => 1,
			'get_filtered'     => [],
		] );

		return new MslsOutput( $options, $collection );
	}

	function test_get_method() {
		$obj = $this->get_test();

		$this->assertIsArray( $obj->get( 0 ) );
	}

	function test___toString_method() {
		$obj = $this->get_test();

		$this->assertIsSTring( $obj->__toString() );
		$this->assertIsSTring( strval( $obj ) );
		$this->assertEquals( $obj->__toString(), strval( $obj ) );
	}

	function test_get_tags_method() {
		$obj = $this->get_test();

		$this->assertIsArray( $obj->get_tags() );
	}

	function test_set_tags_method() {
		Functions\expect( 'wp_parse_args' )->once()->andReturn( [] );

		$obj = $this->get_test();

		$this->assertInstanceOf( MslsOutput::class, $obj->set_tags() );
	}

	function test_is_requirements_not_fulfilled_method_with_null() {
		$obj = $this->get_test();

		$test = $obj->is_requirements_not_fulfilled( null, false, 'de_DE' );
		$this->assertFalse( $test );

		$test = $obj->is_requirements_not_fulfilled( null, true, 'de_DE' );
		$this->assertTrue( $test );
	}

	function test_is_requirements_not_fulfilled_method_with_mslsoptions() {
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		$mydata = new MslsOptions();

		$obj = $this->get_test();

		$test = $obj->is_requirements_not_fulfilled( $mydata, false, 'de_DE' );
		$this->assertFalse( $test );

		$test = $obj->is_requirements_not_fulfilled( $mydata, true, 'de_DE' );
		$this->assertFalse( $test );
	}

	function test_is_requirements_not_fulfilled_method_with_mslsoptionspost() {
		Functions\expect( 'get_option' )->once()->andReturn( [] );

		$mydata = new MslsOptionsPost();

		$obj = $this->get_test();

		$test = $obj->is_requirements_not_fulfilled( $mydata, false, 'de_DE' );
		$this->assertFalse( $test );

		$test = $obj->is_requirements_not_fulfilled( $mydata, true, 'de_DE' );
		$this->assertTrue( $test );
	}

}
