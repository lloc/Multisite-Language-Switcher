<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOutput;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsOptionsPost;
use Brain\Monkey\Functions;

class TestMslsOutput extends MslsUnitTestCase {

	protected function setUp(): void {
		$options = \Mockery::mock( MslsOptions::class );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'has_current_blog' )->andReturn( true );
		$collection->shouldReceive( 'get_current_blog' )->andReturn( 1 );
		$collection->shouldReceive( 'get_filtered' )->andReturn( array() );

		$this->test = new MslsOutput( $options, $collection );
	}

	public function test_get_method(): void {
		$this->assertEquals( array(), $this->test->get( 0 ) );
	}

	public function test_get_alternate_links() {
		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_alpha2' )->andReturn( 'de' );
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_objects' )->andReturn( array( $blog ) );

		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'is_admin' )->once()->andReturn( false );
		Functions\expect( 'is_front_page' )->once()->andReturn( false );
		Functions\expect( 'is_search' )->once()->andReturn( false );
		Functions\expect( 'is_404' )->once()->andReturn( false );

		$this->assertEquals( array(), $this->test->get_alternate_links() );
	}

	public function test___toString() {
		$this->assertIsSTring( $this->test->__toString() );
		$this->assertIsSTring( strval( $this->test ) );
		$this->assertEquals( $this->test->__toString(), strval( $this->test ) );
	}

	public function test_get_tags(): void {
		$this->assertIsArray( $this->test->get_tags() );
	}

	public function test_set_tags(): void {
		Functions\expect( 'wp_parse_args' )->once()->andReturn( array() );

		$this->assertInstanceOf( MslsOutput::class, $this->test->set_tags() );
	}

	public function test_is_requirements_not_fulfilled_with_null(): void {
		$this->assertFalse( $this->test->is_requirements_not_fulfilled( null, false, 'de_DE' ) );
		$this->assertTrue( $this->test->is_requirements_not_fulfilled( null, true, 'de_DE' ) );
	}

	public function test_is_requirements_not_fulfilled_with_mslsoptions(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$mydata = new MslsOptions();

		$this->assertFalse( $this->test->is_requirements_not_fulfilled( $mydata, false, 'de_DE' ) );
		$this->assertFalse( $this->test->is_requirements_not_fulfilled( $mydata, true, 'de_DE' ) );
	}

	public function test_is_requirements_not_fulfilled_with_mslsoptionspost(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );

		$mydata = new MslsOptionsPost();

		$this->assertFalse( $this->test->is_requirements_not_fulfilled( $mydata, false, 'de_DE' ) );
		$this->assertTrue( $this->test->is_requirements_not_fulfilled( $mydata, true, 'de_DE' ) );
	}
}
