<?php

namespace lloc\MslsTests;

use lloc\Msls\MslsBlog;
use lloc\Msls\MslsOptionsTax;
use lloc\Msls\MslsPostTagClassic;
use lloc\Msls\MslsOptions;
use lloc\Msls\MslsBlogCollection;
use Brain\Monkey\Functions;

class WP_Test_MslsPostTagClassic extends Msls_UnitTestCase {

	public function get_sut() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		$collection->shouldReceive( 'get' )->andReturn( [] );

		return new MslsPostTagClassic( $options, $collection );
	}

	public function test_the_input_method() {
		$this->assertFalse( $this->get_sut()->the_input( ( new \stdClass() ), 'test', 'test' ) );
	}

	public function test_add_input() {
		$this->expectOutputString( '<div class="form-field"></div>' );

		$this->get_sut()->add_input( ( new \stdClass() ) );
	}

	public function test_edit_input() {
		$this->expectOutputString( '' );

		$this->get_sut()->edit_input( ( new \stdClass() ) );
	}

	public function test_print_option() {
		$terms = [
			(object) [
				'term_id' => 123,
				'name' => 'pinko',
			]
		];

		Functions\expect( 'switch_to_blog' )->once();
		Functions\expect( 'restore_current_blog' )->once();
		Functions\expect( 'get_post_types' )->once()->andReturn( [] );
		Functions\expect( 'get_terms' )->once()->andReturn( $terms );
		Functions\expect( 'get_edit_post_link' )->once()->andReturn( '/temp' );
		Functions\expect( 'selected' )->once()->andReturn( '' );

		Functions\when( 'plugin_dir_path' )->justReturn( dirname( __DIR__, 1 ) . '/' );

		$blog = \Mockery::mock( MslsBlog::class );
		$blog->shouldReceive( 'get_language' )->once()->andReturn( 'de_DE' );

		$type = 'abc';

		$mydata = \Mockery::mock( MslsOptionsTax::class );
		$mydata->shouldReceive( 'has_value' )->with( 'de_DE' )->andReturn( true );
		$mydata->de_DE = 1;

		$item_format = '<label for="msls_input_%1$s">%2$s</label><select class="msls-translations" name="msls_input_%1$s"><option value=""></option>%3$s</select>';
		$expected = '<label for="msls_input_de_DE"><a title="Edit the translation in the de_DE-blog" href="/temp"><span class="flag-icon flag-icon-de">de_DE</span></a>&nbsp;</label><select class="msls-translations" name="msls_input_de_DE"><option value=""></option><option value="123" >pinko</option></select>';

		$this->expectOutputString( $expected );

		$this->get_sut()->print_option( $blog, $type, $mydata, $item_format );
	}
}