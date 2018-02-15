<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\ContentImport\ImportLogger as Logger;

class ImportLoggerTest extends \Msls_UnitTestCase {

	/**
	 * @var \lloc\Msls\ContentImport\ImportCoordinates
	 */
	public $import_coordinates;

	function setUp() {
		parent::setUp();
		$this->import_coordinates = $this->prophesize( ImportCoordinates::class );
	}

	/**
	 * @test
	 * it should be instantiatable
	 */
	public function it_should_be_instantiatable() {
		$sut = $this->make_instance();

		$this->assertInstanceOf( Logger::class, $sut );
	}

	/**
	 * @return Logger
	 */
	private function make_instance() {
		return new Logger( $this->import_coordinates->reveal() );
	}

	public function testLog_error() {
		$obj = $this->make_instance();
		$obj->log_error( 'some/path/foo', 'foo-bar' );

		$this->assertEquals( 'foo-bar', $obj->get_error( 'some/path/foo' ) );
	}

	public function testLog_success() {
		$obj = $this->make_instance();
		$obj->log_success( 'some/path/foo', 'foo-bar' );

		$this->assertEquals( 'foo-bar', $obj->get_success( 'some/path/foo' ) );
	}

	public function testLog_information() {
		$obj = $this->make_instance();
		$obj->log_information( 'foo', 'bar' );

		$this->assertEquals( 'bar', $obj->get_information( 'foo' ) );
	}

	public function testMerge() {
		$obj_1 = $this->make_instance();
		$obj_2 = $this->make_instance();

		$obj_1->log_information( 'foo', 'bar' );
		$obj_1->log_error( 'foo/one/two', [ 'one' => '23' ] );
		$obj_1->log_error( 'bar/one/two', [ 'one' => '23' ] );
		$obj_1->log_success( 'foo/one/two', [ 'one' => '23' ] );
		$obj_1->log_success( 'bar/one/two', [ 'one' => '23' ] );

		$obj_2->log_information( 'bar', 'foo' );
		$obj_2->log_error( 'foo/one/two', [ 'two' => '89' ] );
		$obj_2->log_error( 'bar/one/two', [ 'two' => '89' ] );
		$obj_2->log_success( 'foo/one/two', [ 'two' => '89' ] );
		$obj_2->log_success( 'bar/one/two', [ 'two' => '89' ] );

		$obj_1->merge( $obj_2 );

		$this->assertEquals( 'foo', $obj_1->get_information( 'bar' ) );
		$this->assertEquals( 'bar', $obj_1->get_information( 'foo' ) );
		$this->assertEquals( [ 'one' => '23', 'two' => '89' ], $obj_1->get_error( 'foo/one/two' ) );
		$this->assertEquals( [ 'one' => '23', 'two' => '89' ], $obj_1->get_error( 'bar/one/two' ) );
		$this->assertEquals( [ 'one' => '23', 'two' => '89' ], $obj_1->get_success( 'foo/one/two' ) );
		$this->assertEquals( [ 'one' => '23', 'two' => '89' ], $obj_1->get_success( 'bar/one/two' ) );
	}

	public function testSave() {
		$this->markTestSkipped( 'Skipped as how and where to log is still undecided' );
	}
}
