<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsWidget;

class TestMslsWidget extends MslsUnitTestCase {

	protected function setUp(): void {
		parent::setUp();

		\Mockery::mock( '\WP_Widget' );

		$this->test = \Mockery::mock( MslsWidget::class )->makePartial();
	}

	public function test_widget(): void {
		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_filtered' )->once()->andReturn( array() );

		$arr = array(
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		);

		Functions\expect( 'wp_parse_args' )->once()->andReturn( $arr );

		$options = \Mockery::mock( MslsOptions::class );

		Functions\expect( 'msls_options' )->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );

		$this->expectOutputString( '<div><h3>Test</h3>No available translations found</div>' );
		$this->test->widget( array(), array( 'title' => 'Test' ) );
	}

	public function test_update(): void {
		Functions\expect( 'wp_strip_all_tags' )->twice()->andReturnFirstArg();

		$result = $this->test->update( array(), array() );
		$this->assertEquals( array(), $result );

		$result = $this->test->update( array( 'title' => 'abc' ), array() );
		$this->assertEquals( array( 'title' => 'abc' ), $result );

		$result = $this->test->update( array( 'title' => 'xyz' ), array( 'title' => 'abc' ) );
		$this->assertEquals( array( 'title' => 'xyz' ), $result );
	}
}
