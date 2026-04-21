<?php declare( strict_types=1 );

namespace lloc\Msls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Injects the "Add from Translation" page-title-action on edit.php.
 *
 * The button is a plain link to MslsTranslationPickerPage; the heavy
 * lifting (listing, filtering, creating) happens on that dedicated page,
 * so this class is intentionally tiny and click-only.
 *
 * @package Msls
 */
class MslsPostListActions {

	/**
	 * @codeCoverageIgnore
	 */
	public static function init(): void {
		$options = msls_options();
		if ( $options->is_excluded() ) {
			return;
		}

		$post_type = msls_post_type()->get_request();
		if ( empty( $post_type ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( ! self::has_source_blogs() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( self::class, 'inject_button' ) );
	}

	/**
	 * Returns true when at least one other blog has MSLS active and could
	 * therefore serve as a translation source.
	 */
	public static function has_source_blogs(): bool {
		$collection = msls_blog_collection();

		foreach ( $collection->get() as $blog ) {
			if ( $collection->is_plugin_active( $blog->userblog_id ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Prints an inline script that appends the link next to core's "Add New".
	 *
	 * WordPress has no server-side hook at the page-title-action slot, so
	 * this is the standard pattern used by many admin plugins.
	 *
	 * @codeCoverageIgnore
	 */
	public static function inject_button(): void {
		$post_type = msls_post_type()->get_request();
		$url       = MslsTranslationPickerPage::url( $post_type );
		$label     = __( 'Add from Translation', 'multisite-language-switcher' );

		$script = sprintf(
			'jQuery(function($){var b=$("<a>").addClass("page-title-action msls-tp-button").attr("href",%1$s).text(%2$s);var $a=$(".wrap .page-title-action").first();if($a.length){$a.after(" ",b);}else{$(".wrap .wp-heading-inline").after(" ",b);}});',
			wp_json_encode( $url ),
			wp_json_encode( $label )
		);

		wp_add_inline_script( 'common', $script );
	}
}
