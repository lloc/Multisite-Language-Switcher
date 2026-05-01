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

	const BASE_SLUG = 'msls-translation-picker';

	const SCRIPT_HANDLE = 'msls-translation-picker';

	const PER_PAGE_OPTION = 'msls_tp_per_page';

	const PER_PAGE_DEFAULT = 20;

	/**
	 * @codeCoverageIgnore
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( self::class, 'register' ) );
		// Late-priority reorder: put our entries right under "All Posts"
		// regardless of what other plugins do to the submenu array.
		add_action( 'admin_menu', array( self::class, 'reorder_submenu' ), 999 );

		add_filter( 'set-screen-option', array( self::class, 'save_per_page_option' ), 10, 3 );
		add_filter( 'set_screen_option_' . self::PER_PAGE_OPTION, array( self::class, 'save_per_page_option' ), 10, 3 );
	}

	/**
	 * Persists the chosen per-page value submitted via screen options.
	 *
	 * @param mixed  $status
	 * @param string $option
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	public static function save_per_page_option( $status, $option, $value ) {
		if ( self::PER_PAGE_OPTION === $option ) {
			$value = (int) $value;
			return $value > 0 ? $value : self::PER_PAGE_DEFAULT;
		}
		return $status;
	}

	/**
	 * Registers a visible submenu entry under each MSLS-supported post
	 * type's "All Posts" menu. This keeps the sidebar expanded on the
	 * right section when the page is active and surfaces the entry point
	 * directly in the menu hierarchy.
	 *
	 * @codeCoverageIgnore
	 */
	public static function register(): void {
		foreach ( MslsPostType::get() as $post_type ) {
			$parent = self::parent_slug( $post_type );
			if ( '' === $parent ) {
				continue;
			}

			$hook = add_submenu_page(
				$parent,
				__( 'Add Post from Translation', 'multisite-language-switcher' ),
				__( 'Add from Translation', 'multisite-language-switcher' ),
				'edit_posts',
				self::page_slug( $post_type ),
				array( self::class, 'render' )
			);

			if ( $hook ) {
				add_action(
					'load-' . $hook,
					static function () use ( $post_type ) {
						MslsTranslationPickerPage::on_page_load( $post_type );
					}
				);
			}
		}
	}

	/**
	 * Called once per request when the picker page is being loaded for a
	 * specific post type. Registers the screen options (per-page and the
	 * automatic column-toggle dropdown).
	 *
	 * @codeCoverageIgnore
	 */
	public static function on_page_load( string $post_type ): void {
		add_screen_option(
			'per_page',
			array(
				'label'   => __( 'Posts per page', 'multisite-language-switcher' ),
				'default' => self::PER_PAGE_DEFAULT,
				'option'  => self::PER_PAGE_OPTION,
			)
		);

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		// Surface the table's columns to WordPress so the screen-options
		// dropdown shows toggles for them and hidden-column user prefs
		// persist through manage{$screen->id}columnshidden.
		add_filter(
			'manage_' . $screen->id . '_columns',
			static function () use ( $post_type ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$source = isset( $_GET['msls_source'] ) ? absint( wp_unslash( (string) $_GET['msls_source'] ) ) : 0;
				$table  = new MslsTranslationPickerTable( $source, $post_type );
				return $table->get_columns();
			}
		);
	}

	/**
	 * Moves each of our submenu entries to the slot directly after the
	 * parent's first item ("All Posts" / "All Pages" / "All CPTs"). Runs
	 * on admin_menu at priority 999 so it applies after every other
	 * plugin has finished adding entries.
	 *
	 * @codeCoverageIgnore
	 */
	public static function reorder_submenu(): void {
		global $submenu;

		if ( ! is_array( $submenu ) ) {
			return;
		}

		foreach ( MslsPostType::get() as $post_type ) {
			$parent = self::parent_slug( $post_type );
			$slug   = self::page_slug( $post_type );

			if ( empty( $submenu[ $parent ] ) || ! is_array( $submenu[ $parent ] ) ) {
				continue;
			}

			$our_key = null;
			foreach ( $submenu[ $parent ] as $k => $item ) {
				if ( isset( $item[2] ) && $slug === $item[2] ) {
					$our_key = $k;
					break;
				}
			}

			if ( null === $our_key ) {
				continue;
			}

			$our_item = $submenu[ $parent ][ $our_key ];
			unset( $submenu[ $parent ][ $our_key ] );

			$rebuilt = array();
			$placed  = false;
			foreach ( $submenu[ $parent ] as $k => $item ) {
				$rebuilt[ $k ] = $item;
				if ( ! $placed ) {
					$rebuilt[] = $our_item;
					$placed    = true;
				}
			}

			if ( ! $placed ) {
				$rebuilt[] = $our_item;
			}

			$submenu[ $parent ] = $rebuilt;
		}
	}

	/**
	 * Menu slug of the parent (Posts / Pages / CPT) menu for a post type.
	 */
	public static function parent_slug( string $post_type ): string {
		if ( '' === $post_type ) {
			return '';
		}
		if ( 'post' === $post_type ) {
			return 'edit.php';
		}
		return 'edit.php?post_type=' . $post_type;
	}

	/**
	 * Unique page slug per post type. Needed because WordPress enforces
	 * globally unique submenu slugs, so we can't reuse one slug under
	 * multiple parents.
	 */
	public static function page_slug( string $post_type ): string {
		return self::BASE_SLUG . '-' . $post_type;
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
			array( 'page' => self::page_slug( $post_type ) ),
			admin_url( self::parent_slug( $post_type ) )
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
					/* translators: 1: index of the current item being processed, 2: total number of selected items */
					'progress'  => __( 'Creating drafts: %1$d of %2$d…', 'multisite-language-switcher' ),
					/* translators: 1: number of drafts successfully created, 2: number of errors encountered */
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
		$page      = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( (string) $_GET['page'] ) ) : '';
		$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( (string) $_GET['post_type'] ) ) : '';
		$source    = isset( $_GET['msls_source'] ) ? absint( wp_unslash( (string) $_GET['msls_source'] ) ) : 0;
		$search    = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['s'] ) ) : '';
		// phpcs:enable

		// Derive post type from the page slug (msls-translation-picker-<post_type>).
		if ( '' !== $page && 0 === strpos( $page, self::BASE_SLUG . '-' ) ) {
			$post_type = substr( $page, strlen( self::BASE_SLUG ) + 1 );
		}

		if ( ! in_array( $post_type, MslsPostType::get(), true ) ) {
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

		// With only one source available, treat it as pre-selected so the
		// user lands straight on the list instead of a picker-of-one.
		if ( 0 === $source && 1 === count( $blogs ) ) {
			$source = (int) $blogs[0]->userblog_id;
		}

		self::enqueue( (int) get_current_blog_id() );

		echo '<div class="wrap msls-tp-page">';
		printf(
			'<p class="msls-tp-back"><a href="%1$s">%2$s</a></p>',
			esc_url( admin_url( self::parent_slug( $post_type ) ) ),
			esc_html__( '← Back to all posts', 'multisite-language-switcher' )
		);
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

		echo '<form method="get" action="', esc_url( admin_url( self::parent_slug( $post_type ) ) ), '" class="msls-tp-filters">';
		echo '<input type="hidden" name="page" value="', esc_attr( self::page_slug( $post_type ) ), '" />';
		if ( 'post' !== $post_type ) {
			echo '<input type="hidden" name="post_type" value="', esc_attr( $post_type ), '" />';
		}
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
					'page'        => self::page_slug( $post_type ),
					'msls_source' => $blog_id,
					's'           => $search,
				),
				admin_url( self::parent_slug( $post_type ) )
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
