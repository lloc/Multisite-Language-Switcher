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
		$terms = [
			(object) [
				'term_id' => 123,
				'name' => 'pinko',
			]
		];

		Functions\stubs( [
			'get_queried_object_id' => 1,
			'is_admin' => true,
			'get_option' => [ 'exists' => true ],
			'get_post_types' => [ 'exists' => true ],
			'switch_to_blog' => true,
			'restore_current_blog' => true,
			'get_terms' => $terms,
			'get_edit_post_link' => '/temp',
			'selected' =>  '',
			'plugin_dir_path' => dirname( __DIR__, 1 ) . '/',
			'get_current_blog_id' => 1,
			'get_admin_url' => '/admin_url',
		] );

		$options = \Mockery::mock( MslsOptions::class );

		$map = [ 'de_DE', 'en_US' ];

		foreach ( $map as $locale ) {
			$blog = \Mockery::mock( MslsBlog::class );
			$blog->shouldReceive( ['get_language' => $locale ] );

			$blogs[] = $blog;
		}

		$collection = \Mockery::mock( MslsBlogCollection::class );
		$collection->shouldReceive( 'get' )->andReturn( $blogs );

		return new MslsPostTagClassic( $options, $collection );
	}

	public function test_add_input() {
		$expected = '<div class="form-field"><h3>Multisite Language Switcher</h3><label for="msls_input_de_DE"><a title="Create a new translation in the de_DE-blog" href="/admin_url"><span class="flag-icon flag-icon-de">de_DE</span></a>&nbsp;</label><select class="msls-translations" name="msls_input_de_DE"><option value=""></option><option value="123" >pinko</option></select><label for="msls_input_en_US"><a title="Create a new translation in the en_US-blog" href="/admin_url"><span class="flag-icon flag-icon-us">en_US</span></a>&nbsp;</label><select class="msls-translations" name="msls_input_en_US"><option value=""></option><option value="123" >pinko</option></select></div>';

		$this->expectOutputString( $expected );

		$this->get_sut()->add_input( null );
	}

	public function test_edit_input() {
		$expected = '<tr><th colspan="2"><strong>Multisite Language Switcher</strong></th></tr><tr class="form-field"><th scope="row" vertical-align="top"><label for="msls_input_de_DE"><a title="Create a new translation in the de_DE-blog" href="/admin_url"><span class="flag-icon flag-icon-de">de_DE</span></a>&nbsp;</label></th><td><select class="msls-translations" name="msls_input_de_DE"><option value=""></option><option value="123" >pinko</option></select></td></tr><tr class="form-field"><th scope="row" vertical-align="top"><label for="msls_input_en_US"><a title="Create a new translation in the en_US-blog" href="/admin_url"><span class="flag-icon flag-icon-us">en_US</span></a>&nbsp;</label></th><td><select class="msls-translations" name="msls_input_en_US"><option value=""></option><option value="123" >pinko</option></select></td></tr>';

		$this->expectOutputString( $expected );

		$this->get_sut()->edit_input( null );
	}

	public function test_print_option() {
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

	public function test_the_input_false() {
		$options    = \Mockery::mock( MslsOptions::class );
		$collection = \Mockery::mock( MslsBlogCollection::class );

		$collection->shouldReceive( 'get' )->andReturn( [] );

		$sut = new MslsPostTagClassic( $options, $collection );
		$this->assertFalse( $sut->the_input( null, 'test', 'test' ) );
	}

}