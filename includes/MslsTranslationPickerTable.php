<?php declare( strict_types=1 );

namespace lloc\Msls;

use lloc\Msls\Query\TranslatedPostIdQuery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * WP_List_Table renderer for untranslated source-blog posts on the
 * MslsTranslationPickerPage.
 *
 * All data is fetched inside a switch_to_blog() on the source blog so
 * pagination, search and permalinks reflect the source, while the
 * target-blog translation map is used to exclude already-translated posts.
 *
 * @package Msls
 */
class MslsTranslationPickerTable extends \WP_List_Table {

	/**
	 * Default page size used as a fallback when the user has not picked a
	 * value via the screen-options dropdown.
	 */
	const PER_PAGE = 20;

	/**
	 * Source blog the listing queries (the picker reads from this site's
	 * data while the page itself runs on the target blog).
	 *
	 * @var int
	 */
	protected int $source_blog_id;

	/**
	 * Post type being listed (read from the URL slug).
	 *
	 * @var string
	 */
	protected string $post_type;

	/**
	 * Optional title-search string forwarded to WP_Query as 's'.
	 *
	 * @var string
	 */
	protected string $search;

	/**
	 * Per-request memo of the source-blog taxonomies whose admin column is
	 * enabled. Resolved once and reused for both column headers and item
	 * rendering.
	 *
	 * @var array<string, \WP_Taxonomy>|null
	 */
	protected ?array $taxonomies_cache = null;

	/**
	 * @param int    $source_blog_id Blog to read posts from.
	 * @param string $post_type      Post type to query.
	 * @param string $search         Optional title-search term.
	 */
	public function __construct( int $source_blog_id, string $post_type, string $search = '' ) {
		parent::__construct(
			array(
				'singular' => 'msls_source_post',
				'plural'   => 'msls_source_posts',
				'ajax'     => false,
				'screen'   => MslsTranslationPickerPage::BASE_SLUG,
			)
		);

		$this->source_blog_id = $source_blog_id;
		$this->post_type      = $post_type;
		$this->search         = $search;
	}

	/**
	 * Returns the column map for the table — checkbox, title, author, any
	 * source-blog taxonomies that opted into show_admin_column, then status
	 * and date.
	 *
	 * @return array<string, string>
	 */
	public function get_columns(): array {
		$cols = array(
			'cb'     => '<input type="checkbox" />',
			'title'  => __( 'Title', 'multisite-language-switcher' ),
			'author' => __( 'Author', 'multisite-language-switcher' ),
		);

		foreach ( $this->get_admin_column_taxonomies() as $name => $tax ) {
			$cols[ 'taxonomy-' . $name ] = $tax->labels->name ?? (string) $tax->label;
		}

		$cols['status'] = __( 'Status', 'multisite-language-switcher' );
		$cols['date']   = __( 'Date', 'multisite-language-switcher' );

		return $cols;
	}

	/**
	 * Returns taxonomies registered for the post type that declared
	 * show_admin_column => true — same signal core edit.php uses to decide
	 * which taxonomy columns to render.
	 *
	 * @return array<string, \WP_Taxonomy>
	 */
	protected function get_admin_column_taxonomies(): array {
		if ( null !== $this->taxonomies_cache ) {
			return $this->taxonomies_cache;
		}

		$switched = false;
		if ( $this->source_blog_id > 0 ) {
			switch_to_blog( $this->source_blog_id );
			$switched = true;
		}

		$this->taxonomies_cache = array();
		foreach ( get_object_taxonomies( $this->post_type, 'objects' ) as $tax ) {
			if ( ! empty( $tax->show_admin_column ) ) {
				$this->taxonomies_cache[ $tax->name ] = $tax;
			}
		}

		if ( $switched ) {
			restore_current_blog();
		}

		return $this->taxonomies_cache;
	}

	/**
	 * Registers the bulk action that the picker JS hijacks to create
	 * drafts for every checked row.
	 *
	 * @return array<string, string>
	 */
	protected function get_bulk_actions(): array {
		return array(
			'msls_bulk_create' => __( 'Create drafts for selected', 'multisite-language-switcher' ),
		);
	}

	/**
	 * Loads the source-blog posts for the current page into $this->items.
	 *
	 * Honours hidden-column user prefs, the per-page screen option, the
	 * search term and pagination. Already-translated post IDs are excluded
	 * via TranslatedPostIdQuery against the target language.
	 */
	public function prepare_items(): void {
		$columns               = $this->get_columns();
		$hidden                = is_object( $this->screen ) ? get_hidden_columns( $this->screen ) : array();
		$this->_column_headers = array( $columns, $hidden, array() );

		$target_lang  = MslsBlogCollection::get_blog_language( get_current_blog_id() );
		$current_page = $this->get_pagenum();
		$per_page     = (int) $this->get_items_per_page( MslsTranslationPickerPage::PER_PAGE_OPTION, self::PER_PAGE );

		switch_to_blog( $this->source_blog_id );

		// Cache key includes the source blog id so a non-blog-aware object
		// cache backend can't leak ids from one switched-to blog to another.
		$cache_params   = array( __METHOD__, (string) $this->source_blog_id, $target_lang );
		$translated_ids = ( new TranslatedPostIdQuery( MslsSqlCacher::create( __CLASS__, $cache_params ) ) )( $target_lang );

		$args = array(
			'post_type'      => $this->post_type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'future' ),
			'posts_per_page' => $per_page,
			'paged'          => $current_page,
			'post__not_in'   => $translated_ids,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( '' !== $this->search ) {
			$args['s'] = $this->search;
		}

		$query      = new \WP_Query( $args );
		$items      = array();
		$taxonomies = array_keys( $this->get_admin_column_taxonomies() );

		foreach ( $query->posts as $post ) {
			$terms_by_tax = array();
			foreach ( $taxonomies as $tax_name ) {
				$terms                     = get_the_terms( $post->ID, $tax_name );
				$terms_by_tax[ $tax_name ] = is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array();
			}

			$items[] = array(
				'ID'         => (int) $post->ID,
				'title'      => get_the_title( $post ),
				'status'     => $post->post_status,
				'date'       => get_the_date( '', $post ),
				// Preview URL for non-published statuses so reviewers
				// can view drafts/pending without hitting a public 404.
				'permalink'  => 'publish' === $post->post_status
					? get_permalink( $post )
					: get_preview_post_link( $post ),
				'author'     => (string) get_the_author_meta( 'display_name', (int) $post->post_author ),
				'taxonomies' => $terms_by_tax,
			);
		}

		$total = (int) $query->found_posts;

		restore_current_blog();

		$this->items = $items;

		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
				'total_pages' => $per_page > 0 ? (int) ceil( $total / $per_page ) : 0,
			)
		);
	}

	/**
	 * @param array<string, mixed> $item
	 */
	protected function column_cb( $item ): string {
		return sprintf(
			'<input type="checkbox" name="post[]" value="%d" />',
			(int) $item['ID']
		);
	}

	/**
	 * @param array<string, mixed> $item
	 */
	protected function column_title( $item ): string {
		$title = '<strong>' . esc_html( $item['title'] ) . '</strong>';

		$actions = array(
			'view'   => sprintf(
				'<a class="msls-tp-view" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
				esc_url( (string) $item['permalink'] ),
				esc_html__( 'View original', 'multisite-language-switcher' )
			),
			'create' => sprintf(
				'<button type="button" class="button-link msls-tp-create" data-source-post-id="%1$d" data-source-blog-id="%2$d">%3$s</button>',
				(int) $item['ID'],
				$this->source_blog_id,
				esc_html__( 'Create draft', 'multisite-language-switcher' )
			),
		);

		return $title . $this->row_actions( $actions, true );
	}

	/**
	 * @param array<string, mixed> $item
	 */
	protected function column_status( $item ): string {
		$labels = array(
			'publish' => __( 'Published', 'multisite-language-switcher' ),
			'draft'   => __( 'Draft', 'multisite-language-switcher' ),
			'pending' => __( 'Pending', 'multisite-language-switcher' ),
			'future'  => __( 'Scheduled', 'multisite-language-switcher' ),
		);
		$key    = (string) $item['status'];
		$label  = $labels[ $key ] ?? $key;

		return sprintf(
			'<span class="msls-tp-status-badge msls-tp-status-%1$s">%2$s</span>',
			esc_attr( $key ),
			esc_html( $label )
		);
	}

	/**
	 * @param array<string, mixed> $item
	 */
	protected function column_date( $item ): string {
		return esc_html( (string) $item['date'] );
	}

	/**
	 * @param array<string, mixed> $item
	 */
	protected function column_author( $item ): string {
		return '' !== (string) $item['author'] ? esc_html( (string) $item['author'] ) : '—';
	}

	/**
	 * Fallback for the dynamic taxonomy-* columns.
	 *
	 * @param array<string, mixed> $item
	 * @param string               $column_name
	 */
	protected function column_default( $item, $column_name ): string {
		if ( 0 === strpos( $column_name, 'taxonomy-' ) ) {
			$tax   = substr( $column_name, strlen( 'taxonomy-' ) );
			$names = $item['taxonomies'][ $tax ] ?? array();
			return empty( $names ) ? '—' : esc_html( implode( ', ', $names ) );
		}
		return '—';
	}

	/**
	 * Renders the empty-state message — distinguishes between "search
	 * returned nothing" and "every source post already has a translation".
	 */
	public function no_items(): void {
		if ( '' !== $this->search ) {
			esc_html_e( 'No posts match your search.', 'multisite-language-switcher' );
		} else {
			esc_html_e( 'All source posts are already translated.', 'multisite-language-switcher' );
		}
	}
}
