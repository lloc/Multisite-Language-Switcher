<?php declare( strict_types=1 );

namespace lloc\MslsTests\Admin;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use lloc\Msls\Admin\CustomFilter;
use lloc\Msls\Blog\Blog;
use lloc\Msls\Blog\Collection;
use lloc\Msls\Options\Options;
use lloc\Msls\Request\Fields;
use lloc\MslsTests\MslsUnitTestCase;

final class TestCustomFilter extends MslsUnitTestCase {

	private function CustomFilterFactory(): CustomFilter {
		$options = \Mockery::mock( Options::class );

		$blog              = \Mockery::mock( Blog::class );
		$blog->userblog_id = 1;
		$blog->shouldReceive( 'get_language' )->andReturn( 'de_DE' );
		$blog->shouldReceive( 'get_description' )->andReturn( 'Deutsch' );

		$collection = \Mockery::mock( Collection::class );
		$collection->shouldReceive( 'get' )->andReturns( array( $blog ) );
		$collection->shouldReceive( 'get_object' )->with( 1 )->andReturns( $blog );
		$collection->shouldReceive( 'get_object' )->with( 2 )->andReturns( null );

		return new CustomFilter( $options, $collection );
	}

	public function test_execute_filter(): void {
		$query = \Mockery::mock( 'WP_Query' );

		$test = $this->CustomFilterFactory();

		$this->assertFalse( $test->execute_filter( $query ) );
	}

	public function test_execute_filter_with_filter_input(): void {
		global $wpdb;

		$result = array(
			(object) array(
				'option_id'   => 1,
				'option_name' => 'msls_123',
			),
		);

		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, Fields::FIELD_MSLS_FILTER )->andReturn( true );
		Functions\expect( 'filter_input' )->once()->andReturn( '1' );
		Functions\expect( 'wp_cache_get' )->once()->andReturn( $result );

		$wpdb = \Mockery::mock( '\wpdb' );
		$wpdb->shouldReceive( 'prepare' )->once();

		$query = \Mockery::mock( '\WP_Query' );

		$test = $this->CustomFilterFactory();
		$this->assertInstanceOf( '\WP_Query', $test->execute_filter( $query ) );
	}

	public function test_execute_filter_with_filter_but_no_blog(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, Fields::FIELD_MSLS_FILTER )->andReturn( true );
		Functions\expect( 'filter_input' )->once()->andReturn( '2' );

		$query = \Mockery::mock( '\WP_Query' );

		$test = $this->CustomFilterFactory();
		$this->assertFalse( $test->execute_filter( $query ) );
	}

	public function test_add_filter(): void {
		Functions\expect( 'filter_has_var' )->once()->with( INPUT_GET, Fields::FIELD_MSLS_FILTER )->andReturn( true );
		Functions\expect( 'filter_input' )->once()->andReturn( '1' );
		Functions\expect( 'selected' )->once()->with( '1', '1', false )->andReturn( 'selected="selected"' );

		Filters\expectApplied( 'msls_input_select_name' )->once()->andReturn( Fields::FIELD_MSLS_FILTER );

		$this->expectOutputString( '<select id="msls_filter" name="msls_filter"><option value="" >Show all posts</option><option value="1" selected="selected">Not translated in the Deutsch-blog</option></select>' );

		$test = $this->CustomFilterFactory();
		$test->add_filter();
	}

	public function test_add_no_selected_blog(): void {
		Functions\expect( 'selected' )->twice()->andReturn( '' );

		Filters\expectApplied( 'msls_input_select_name' )->once()->andReturn( Fields::FIELD_MSLS_FILTER );

		$this->expectOutputString( '<select id="msls_filter" name="msls_filter"><option value="" >Show all posts</option><option value="1" >Not translated in the Deutsch-blog</option></select>' );

		$test = $this->CustomFilterFactory();
		$test->add_filter();
	}
}
