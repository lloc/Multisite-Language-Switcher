<?php declare( strict_types=1 );

namespace lloc\MslsTests\Admin\TranslationPicker;

use Brain\Monkey\Functions;
use lloc\Msls\Admin\TranslationPicker\Page;
use lloc\MslsTests\MslsUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class TestPage extends MslsUnitTestCase {

	/**
	 * @return array<string, array{string, string}>
	 */
	public static function page_slug_provider(): array {
		return array(
			'post'  => array( 'post', 'msls-translation-picker-post' ),
			'page'  => array( 'page', 'msls-translation-picker-page' ),
			'event' => array( 'event', 'msls-translation-picker-event' ),
		);
	}

	#[DataProvider( 'page_slug_provider' )]
	public function test_page_slug_includes_post_type( string $post_type, string $expected ): void {
		$this->assertSame( $expected, Page::page_slug( $post_type ) );
	}

	/**
	 * Only the built-in post type lives on the bare edit.php, and an empty post type has
	 * no parent at all.
	 *
	 * @return array<string, array{string, string}>
	 */
	public static function parent_slug_provider(): array {
		return array(
			'built-in post'   => array( 'post', 'edit.php' ),
			'page'            => array( 'page', 'edit.php?post_type=page' ),
			'custom type'     => array( 'event', 'edit.php?post_type=event' ),
			'empty post type' => array( '', '' ),
		);
	}

	#[DataProvider( 'parent_slug_provider' )]
	public function test_parent_slug( string $post_type, string $expected ): void {
		$this->assertSame( $expected, Page::parent_slug( $post_type ) );
	}

	public function test_url_uses_admin_url_and_query_arg(): void {
		Functions\expect( 'admin_url' )
			->once()
			->with( 'edit.php' )
			->andReturn( 'https://example.tld/wp-admin/edit.php' );

		Functions\expect( 'add_query_arg' )
			->once()
			->andReturnUsing(
				function ( $args, $url ) {
					return $url . '?' . http_build_query( $args );
				}
			);

		$result = Page::url( 'post' );

		$this->assertSame(
			'https://example.tld/wp-admin/edit.php?page=msls-translation-picker-post',
			$result
		);
	}

	public function test_url_for_non_post_post_type_routes_through_typed_parent(): void {
		Functions\expect( 'admin_url' )
			->once()
			->with( 'edit.php?post_type=page' )
			->andReturn( 'https://example.tld/wp-admin/edit.php?post_type=page' );

		Functions\expect( 'add_query_arg' )
			->once()
			->andReturnUsing(
				function ( $args, $url ) {
					return $url . '&' . http_build_query( $args );
				}
			);

		$result = Page::url( 'page' );

		$this->assertSame(
			'https://example.tld/wp-admin/edit.php?post_type=page&page=msls-translation-picker-page',
			$result
		);
	}

	/**
	 * @return array<string, array{string, string, int|false}>
	 */
	public static function save_per_page_option_provider(): array {
		return array(
			'picker option'            => array( 'msls_tp_per_page', '42', 42 ),
			'non-positive value'       => array( 'msls_tp_per_page', '0', Page::PER_PAGE_DEFAULT ),
			'unrelated option ignored' => array( 'unrelated_option', '5', false ),
		);
	}

	/**
	 * @param int|false $expected
	 */
	#[DataProvider( 'save_per_page_option_provider' )]
	public function test_save_per_page_option( string $option, string $value, $expected ): void {
		$this->assertSame( $expected, Page::save_per_page_option( false, $option, $value ) );
	}
}
