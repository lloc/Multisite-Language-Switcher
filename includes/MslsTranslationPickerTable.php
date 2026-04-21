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

	const PER_PAGE = 20;

	protected int $source_blog_id;

	protected string $post_type;

	protected string $search;

	public function __construct( int $source_blog_id, string $post_type, string $search = '' ) {
		parent::__construct(
			array(
				'singular' => 'msls_source_post',
				'plural'   => 'msls_source_posts',
				'ajax'     => false,
				'screen'   => MslsTranslationPickerPage::SLUG,
			)
		);

		$this->source_blog_id = $source_blog_id;
		$this->post_type      = $post_type;
		$this->search         = $search;
	}

	public function get_columns(): array {
		return array(
			'cb'      => '<input type="checkbox" />',
			'title'   => __( 'Title', 'multisite-language-switcher' ),
			'status'  => __( 'Status', 'multisite-language-switcher' ),
			'date'    => __( 'Date', 'multisite-language-switcher' ),
			'actions' => __( 'Actions', 'multisite-language-switcher' ),
		);
	}

	protected function get_bulk_actions(): array {
		return array(
			'msls_bulk_create' => __( 'Create drafts for selected', 'multisite-language-switcher' ),
		);
	}

	public function prepare_items(): void {
		$this->_column_headers = array( $this->get_columns(), array(), array() );

		$target_lang  = MslsBlogCollection::get_blog_language( get_current_blog_id() );
		$current_page = $this->get_pagenum();

		switch_to_blog( $this->source_blog_id );

		$translated_ids = ( new TranslatedPostIdQuery( MslsSqlCacher::create( __CLASS__, __METHOD__ ) ) )( $target_lang );

		$args = array(
			'post_type'      => $this->post_type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'future' ),
			'posts_per_page' => self::PER_PAGE,
			'paged'          => $current_page,
			'post__not_in'   => $translated_ids,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( '' !== $this->search ) {
			$args['s'] = $this->search;
		}

		$query = new \WP_Query( $args );
		$items = array();

		foreach ( $query->posts as $post ) {
			$items[] = array(
				'ID'        => (int) $post->ID,
				'title'     => get_the_title( $post ),
				'status'    => $post->post_status,
				'date'      => get_the_date( '', $post ),
				'permalink' => get_permalink( $post ),
			);
		}

		$total = (int) $query->found_posts;

		restore_current_blog();

		$this->items = $items;

		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => self::PER_PAGE,
				'total_pages' => (int) ceil( $total / self::PER_PAGE ),
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
		return '<strong>' . esc_html( $item['title'] ) . '</strong>';
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
	protected function column_actions( $item ): string {
		$view = sprintf(
			'<a class="msls-tp-view" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( (string) $item['permalink'] ),
			esc_html__( 'View original', 'multisite-language-switcher' )
		);

		$create = sprintf(
			'<button type="button" class="button button-primary msls-tp-create" data-source-post-id="%1$d" data-source-blog-id="%2$d">%3$s</button>',
			(int) $item['ID'],
			$this->source_blog_id,
			esc_html__( 'Create draft', 'multisite-language-switcher' )
		);

		return $view . ' ' . $create;
	}

	public function no_items(): void {
		if ( '' !== $this->search ) {
			esc_html_e( 'No posts match your search.', 'multisite-language-switcher' );
		} else {
			esc_html_e( 'All source posts are already translated.', 'multisite-language-switcher' );
		}
	}
}
