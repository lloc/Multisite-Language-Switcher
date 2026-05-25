<?php declare( strict_types=1 );

namespace lloc\MslsTests\Admin\TranslationPicker;

use Brain\Monkey\Functions;
use lloc\Msls\Admin\TranslationPicker\Page;
use lloc\MslsTests\MslsUnitTestCase;

final class TestPage extends MslsUnitTestCase {

	public function test_page_slug_includes_post_type(): void {
		$this->assertSame( 'msls-translation-picker-post', Page::page_slug( 'post' ) );
		$this->assertSame( 'msls-translation-picker-page', Page::page_slug( 'page' ) );
		$this->assertSame( 'msls-translation-picker-event', Page::page_slug( 'event' ) );
	}

	public function test_parent_slug_for_built_in_post(): void {
		$this->assertSame( 'edit.php', Page::parent_slug( 'post' ) );
	}

	public function test_parent_slug_for_other_post_types(): void {
		$this->assertSame( 'edit.php?post_type=page', Page::parent_slug( 'page' ) );
		$this->assertSame( 'edit.php?post_type=event', Page::parent_slug( 'event' ) );
	}

	public function test_parent_slug_for_empty_post_type(): void {
		$this->assertSame( '', Page::parent_slug( '' ) );
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

	public function test_save_per_page_option_returns_int_for_picker_option(): void {
		$this->assertSame(
			42,
			Page::save_per_page_option( false, 'msls_tp_per_page', '42' )
		);
	}

	public function test_save_per_page_option_falls_back_for_non_positive(): void {
		$this->assertSame(
			Page::PER_PAGE_DEFAULT,
			Page::save_per_page_option( false, 'msls_tp_per_page', '0' )
		);
	}

	public function test_save_per_page_option_passes_through_other_options(): void {
		$this->assertFalse(
			Page::save_per_page_option( false, 'unrelated_option', '5' )
		);
	}
}
