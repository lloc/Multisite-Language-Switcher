<?php declare( strict_types=1 );

namespace lloc\MslsTests;

use Brain\Monkey\Functions;
use lloc\Msls\MslsTranslationPickerPage;

final class TestMslsTranslationPickerPage extends MslsUnitTestCase {

	public function test_page_slug_includes_post_type(): void {
		$this->assertSame( 'msls-translation-picker-post', MslsTranslationPickerPage::page_slug( 'post' ) );
		$this->assertSame( 'msls-translation-picker-page', MslsTranslationPickerPage::page_slug( 'page' ) );
		$this->assertSame( 'msls-translation-picker-event', MslsTranslationPickerPage::page_slug( 'event' ) );
	}

	public function test_parent_slug_for_built_in_post(): void {
		$this->assertSame( 'edit.php', MslsTranslationPickerPage::parent_slug( 'post' ) );
	}

	public function test_parent_slug_for_other_post_types(): void {
		$this->assertSame( 'edit.php?post_type=page', MslsTranslationPickerPage::parent_slug( 'page' ) );
		$this->assertSame( 'edit.php?post_type=event', MslsTranslationPickerPage::parent_slug( 'event' ) );
	}

	public function test_parent_slug_for_empty_post_type(): void {
		$this->assertSame( '', MslsTranslationPickerPage::parent_slug( '' ) );
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

		$result = MslsTranslationPickerPage::url( 'post' );

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

		$result = MslsTranslationPickerPage::url( 'page' );

		$this->assertSame(
			'https://example.tld/wp-admin/edit.php?post_type=page&page=msls-translation-picker-page',
			$result
		);
	}

	public function test_save_per_page_option_returns_int_for_picker_option(): void {
		$this->assertSame(
			42,
			MslsTranslationPickerPage::save_per_page_option( false, 'msls_tp_per_page', '42' )
		);
	}

	public function test_save_per_page_option_falls_back_for_non_positive(): void {
		$this->assertSame(
			MslsTranslationPickerPage::PER_PAGE_DEFAULT,
			MslsTranslationPickerPage::save_per_page_option( false, 'msls_tp_per_page', '0' )
		);
	}

	public function test_save_per_page_option_passes_through_other_options(): void {
		$this->assertFalse(
			MslsTranslationPickerPage::save_per_page_option( false, 'unrelated_option', '5' )
		);
	}
}
