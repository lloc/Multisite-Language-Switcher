<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsBlogCollection;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsOutput;
use lloc\Msls\MslsWidget;

final class TestMslsWidget extends MslsUnitTestCase {

	public function test_init(): void {
		$options = \Mockery::mock( MslsOptions::class );
		$options->shouldReceive( 'is_excluded' )->once()->andReturn( false );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'register_widget' )->once()->with( MslsWidget::class );

		$this->expectNotToPerformAssertions();

		MslsWidget::init();
	}

	public function test_widget(): void {
		$arr = array(
			'before_widget' => '<div>',
			'after_widget'  => '</div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		);

		Functions\expect( 'wp_parse_args' )->once()->andReturn( $arr );

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get_filtered' )->once()->andReturn( array() );

		$options = \Mockery::mock( MslsOptions::class );

		Functions\expect( 'msls_options' )->once()->andReturn( $options );
		Functions\expect( 'msls_blog_collection' )->once()->andReturn( $collection );
		Functions\expect( 'msls_output' )->once()->andReturn( MslsOutput::create() );

		$this->expectOutputString( '<div><h3>Test</h3>No available translations found</div>' );
		( new MslsWidget() )->widget( array(), array( 'title' => 'Test' ) );
	}

	public static function update_provider(): array {
		return array(
			array( array(), array(), array(), 0 ),
			array( array( 'title' => 'abc' ), array(), array( 'title' => 'abc' ), 1 ),
			array( array( 'title' => 'xyz' ), array( 'title' => 'abc' ), array( 'title' => 'xyz' ), 1 ),
		);
	}

	/**
	 * @dataProvider update_provider
	 */
	public function test_update( array $new_instance, array $old_instance, array $expected, int $times ): void {
		Functions\expect( 'wp_strip_all_tags' )->times( $times )->andReturnFirstArg();

		$result = ( new MslsWidget() )->update( $new_instance, $old_instance );
		$this->assertEquals( $expected, $result );
	}

	public function test_form(): void {
		$expected = '<p><label for="field-id-title">Title:</label> <input class="widefat" id="field-id-title" name="field-name-title" type="text" value="" /></p>';

		$this->expectOutputString( $expected );

		$result = ( new MslsWidget() )->form( array() );
		$this->assertEquals( $expected, $result );
	}
}
