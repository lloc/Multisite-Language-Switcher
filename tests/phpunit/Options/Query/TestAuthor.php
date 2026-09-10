<?php declare( strict_types=1 );

namespace lloc\MslsTests\Options\Query;

use Brain\Monkey\Functions;
use lloc\Msls\Db\SqlCacher;
use lloc\Msls\Options\Query\Author;
use lloc\MslsTests\MslsUnitTestCase;

use function Brain\Monkey\Functions;

final class TestAuthor extends MslsUnitTestCase {

	private function OptionsQueryAuthorFactory( int $author_id ): Author {
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( $author_id );

		$sql_cacher = \Mockery::mock( SqlCacher::class );
		$sql_cacher->shouldReceive( 'prepare' )->andReturn( 'SQL Query String' );
		$sql_cacher->shouldReceive( 'get_var' )->andReturn( random_int( 1, 10 ) );

		return new Author( $sql_cacher );
	}

	public function test_has_value_true(): void {
		$this->assertTrue( $this->OptionsQueryAuthorFactory( 17 )->has_value( 'de_DE' ) );
	}

	public function test_has_value_false(): void {
		$this->assertFalse( $this->OptionsQueryAuthorFactory( 0 )->has_value( 'de_DE' ) );
	}

	public function test_get_max_pages_from_the_counted_posts(): void {
		Functions\expect( 'get_option' )->once()->andReturn( array() );
		Functions\expect( 'get_queried_object_id' )->once()->andReturn( 17 );

		$sql_cacher = \Mockery::mock( SqlCacher::class );
		$sql_cacher->shouldReceive( 'prepare' )->andReturn( 'SQL Query String' );
		$sql_cacher->shouldReceive( 'get_var' )->andReturn( 25 );

		$test = new Author( $sql_cacher );

		Functions\expect( 'get_option' )->once()->with( 'posts_per_page', 10 )->andReturn( 10 );

		$this->assertEquals( 3, $test->get_max_pages( 'de_DE', Author::PAGINATION_ARCHIVE ) );
	}

	public function test_get_max_pages_without_posts(): void {
		$this->assertEquals( 0, $this->OptionsQueryAuthorFactory( 0 )->get_max_pages( 'de_DE', Author::PAGINATION_ARCHIVE ) );
	}

	public function test_get_max_pages_of_a_post_split_by_nextpage(): void {
		$this->assertEquals( 0, $this->OptionsQueryAuthorFactory( 17 )->get_max_pages( 'de_DE', Author::PAGINATION_SINGLE ) );
	}

	public function test_get_current_link_method(): void {
		Functions\expect( 'get_author_posts_url' )->once()->andReturn( 'https://msls.co/queried-author' );

		$this->assertEquals( 'https://msls.co/queried-author', $this->OptionsQueryAuthorFactory( 42 )->get_current_link() );
	}
}
