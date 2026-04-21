<?php declare( strict_types=1 );

namespace lloc\Msls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dedicated admin page that replaces the former Thickbox modal for
 * creating translation drafts from another blog.
 *
 * Rendered at wp-admin/admin.php?page=msls-translation-picker&post_type=...
 * Registered as a hidden submenu (no menu entry) so the only entry point is
 * the "Add from Translation" button on edit.php.
 *
 * @package Msls
 */
class MslsTranslationPickerPage {

	const SLUG = 'msls-translation-picker';

	const SCRIPT_HANDLE = 'msls-translation-picker';

	/**
	 * @codeCoverageIgnore
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( self::class, 'register' ) );
	}

	/**
	 * Registers a hidden submenu page so the URL is routable but no menu
	 * item is printed.
	 *
	 * @codeCoverageIgnore
	 */
	public static function register(): void {
		add_submenu_page(
			'',
			__( 'Add Post from Translation', 'multisite-language-switcher' ),
			__( 'Add Post from Translation', 'multisite-language-switcher' ),
			'edit_posts',
			self::SLUG,
			array( self::class, 'render' )
		);
	}

	/**
	 * Canonical URL for the page, scoped to a post type.
	 *
	 * @param string $post_type
	 *
	 * @return string
	 */
	public static function url( string $post_type ): string {
		return add_query_arg(
			array(
				'page'      => self::SLUG,
				'post_type' => $post_type,
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Enqueues the picker script on this page only.
	 *
	 * @codeCoverageIgnore
	 */
	public static function enqueue( int $target_blog_id ): void {
		$ver    = defined( 'MSLS_PLUGIN_VERSION' ) ? constant( 'MSLS_PLUGIN_VERSION' ) : false;
		$folder = defined( 'SCRIPT_DEBUG' ) && constant( 'SCRIPT_DEBUG' ) ? 'src' : 'assets/js';

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			MslsPlugin::plugins_url( "$folder/msls-translation-picker.js" ),
			array( 'jquery', 'wp-api-fetch' ),
			$ver,
			array( 'in_footer' => true )
		);

		wp_localize_script(
			self::SCRIPT_HANDLE,
			'mslsTranslationPicker',
			array(
				'targetBlogId' => $target_blog_id,
				'i18n'         => array(
					'creating'  => __( 'Creating draft…', 'multisite-language-switcher' ),
					'progress'  => __( 'Creating drafts: %1$d of %2$d…', 'multisite-language-switcher' ),
					'completed' => __( '%1$d drafts created, %2$d errors.', 'multisite-language-switcher' ),
					'noneChose' => __( 'No posts selected.', 'multisite-language-switcher' ),
					'error'     => __( 'Something went wrong. Please try again.', 'multisite-language-switcher' ),
				),
			)
		);
	}

	/**
	 * Renders the page.
	 *
	 * @codeCoverageIgnore
	 */
	public static function render(): void {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'multisite-language-switcher' ) );
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( (string) $_GET['post_type'] ) ) : 'post';
		$source    = isset( $_GET['msls_source'] ) ? absint( wp_unslash( (string) $_GET['msls_source'] ) ) : 0;
		$search    = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['s'] ) ) : '';
		// phpcs:enable

		if ( ! in_array( $post_type, msls_post_type()::get(), true ) ) {
			$post_type = 'post';
		}

		$collection = msls_blog_collection();
		$target     = $collection->get_current_blog();
		$blogs      = array();
		foreach ( $collection->get() as $blog ) {
			if ( $collection->is_plugin_active( $blog->userblog_id ) ) {
				$blogs[] = $blog;
			}
		}

		self::enqueue( (int) get_current_blog_id() );

		echo '<div class="wrap msls-tp-page">';
		printf(
			'<h1 class="wp-heading-inline">%s</h1>',
			esc_html__( 'Add Post from Translation', 'multisite-language-switcher' )
		);
		echo '<hr class="wp-header-end" />';

		if ( $target instanceof MslsBlog ) {
			printf(
				'<div class="notice notice-info inline msls-tp-banner"><p><span class="msls-tp-banner-arrow" aria-hidden="true">→</span> %1$s <strong>%2$s</strong> <span class="msls-tp-lang-chip">%3$s</span></p></div>',
				esc_html__( 'Creating drafts in:', 'multisite-language-switcher' ),
				esc_html( $target->get_description() ),
				esc_html( strtoupper( $target->get_alpha2() ) )
			);
		}

		self::render_filter_form( $post_type, $source, $search, $blogs );

		if ( $source > 0 ) {
			self::render_list_table( $source, $post_type, $search );
		} else {
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Choose a source blog to list untranslated posts.', 'multisite-language-switcher' )
			);
		}

		echo '</div>';
	}

	/**
	 * @param string               $post_type
	 * @param int                  $source
	 * @param string               $search
	 * @param array<int, MslsBlog> $blogs
	 *
	 * @codeCoverageIgnore
	 */
	private static function render_filter_form( string $post_type, int $source, string $search, array $blogs ): void {
		self::render_source_flags( $post_type, $source, $search, $blogs );

		echo '<form method="get" action="', esc_url( admin_url( 'admin.php' ) ), '" class="msls-tp-filters">';
		echo '<input type="hidden" name="page" value="', esc_attr( self::SLUG ), '" />';
		echo '<input type="hidden" name="post_type" value="', esc_attr( $post_type ), '" />';
		echo '<input type="hidden" name="msls_source" value="', esc_attr( (string) $source ), '" />';

		printf(
			'<input type="search" id="msls-tp-search" name="s" value="%1$s" placeholder="%2$s" />',
			esc_attr( $search ),
			esc_attr__( 'Filter by title…', 'multisite-language-switcher' )
		);

		printf(
			' <button type="submit" class="button">%s</button>',
			esc_html__( 'Apply', 'multisite-language-switcher' )
		);

		echo '</form>';
	}

	/**
	 * Renders a row of clickable flag-buttons — one per source blog.
	 * Navigating between sources no longer needs a select + Apply.
	 *
	 * @param string               $post_type
	 * @param int                  $source
	 * @param string               $search
	 * @param array<int, MslsBlog> $blogs
	 *
	 * @codeCoverageIgnore
	 */
	private static function render_source_flags( string $post_type, int $source, string $search, array $blogs ): void {
		if ( empty( $blogs ) ) {
			return;
		}

		echo '<div class="msls-tp-sources" role="tablist" aria-label="',
			esc_attr__( 'Source blog', 'multisite-language-switcher' ), '">';

		printf(
			'<span class="msls-tp-sources-label">%s</span>',
			esc_html__( 'Source blog:', 'multisite-language-switcher' )
		);

		foreach ( $blogs as $blog ) {
			$blog_id   = (int) $blog->userblog_id;
			$is_active = ( $source === $blog_id );

			$icon = ( new MslsAdminIcon( null ) )
				->set_language( $blog->get_language() )
				->set_icon_type( MslsAdminIcon::TYPE_FLAG )
				->get_icon();

			$url = add_query_arg(
				array(
					'page'        => self::SLUG,
					'post_type'   => $post_type,
					'msls_source' => $blog_id,
					's'           => $search,
				),
				admin_url( 'admin.php' )
			);

			printf(
				'<a href="%1$s" class="msls-tp-source-flag%2$s" role="tab" aria-selected="%3$s" title="%4$s">%5$s<span class="msls-tp-source-label">%6$s</span></a>',
				esc_url( $url ),
				$is_active ? ' is-active' : '',
				$is_active ? 'true' : 'false',
				esc_attr( $blog->get_description() ),
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$icon,
				esc_html( $blog->get_description() )
			);
		}

		echo '</div>';
	}

	/**
	 * @codeCoverageIgnore
	 */
	private static function render_list_table( int $source, string $post_type, string $search ): void {
		if ( ! class_exists( '\\WP_List_Table' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}

		$table = new MslsTranslationPickerTable( $source, $post_type, $search );
		$table->prepare_items();

		echo '<form method="post" id="msls-tp-form">';
		wp_nonce_field( 'msls_tp_bulk', 'msls_tp_nonce' );
		$table->display();
		echo '</form>';
	}
}
