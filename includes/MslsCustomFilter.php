<?php
/**
 * MslsCustomFilter
 * @author Maciej Czerpiński <contact@speccode.com>
 * @contributor Dennis Ploetner <re@lloc.de>
 * @since 0.9.9
 */

/**
 * Adding custom filter to posts/pages table.
 * @package Msls
 */
class MslsCustomFilter extends MslsMain {

	/**
	 * Init
	 * @return MslsCustomFilter
	 */
	public static function init() {
		$obj     = new self();
		$options = MslsOptions::instance();
		if ( ! $options->is_excluded() ) {
			$post_type = MslsPostType::instance()->get_request();
			if ( ! empty( $post_type ) ) {
				add_action( 'restrict_manage_posts', array( $obj, 'add_filter' ) );
				add_filter( 'parse_query',           array( $obj, 'execute_filter' ) );
			}
		}
		return $obj;
	}

	/**
	 * Echo's select tag with list of blogs
	 * @uses selected
	 */
	public function add_filter() {
		$id = (
			filter_has_var( INPUT_GET, 'msls_filter' ) ?
			filter_input( INPUT_GET, 'msls_filter', FILTER_SANITIZE_NUMBER_INT ) :
			''
		);

		$blogs = MslsBlogCollection::instance()->get();
		if ( $blogs ) {
			echo '<select name="msls_filter" id="msls_filter">';
			echo '<option value="">' . esc_html( __( 'Show all blogs', 'multisite-language-switcher' ) ) . '</option>';
			foreach ( $blogs as $blog ) {
				printf(
					'<option value="%d" %s>%s</option>',
					$blog->userblog_id,
					selected( $id, $blog->userblog_id, false ),
					sprintf( __( 'Not translated in the %s-blog', 'multisite-language-switcher' ), $blog->get_description() )
				);
			}
			echo '</select>';
		}
	}

	/**
	 * Execute filter. Exclude translated posts from WP_Query
	 * @param WP_Query $query
	 * @return false or WP_Query object
	 */
	public function execute_filter( WP_Query $query ) {
		$blogs = MslsBlogCollection::instance()->get();

		if ( ! filter_has_var( INPUT_GET, 'msls_filter' ) ) {
			return false;
		}

		$id = filter_input( INPUT_GET, 'msls_filter', FILTER_SANITIZE_NUMBER_INT );

		if ( isset( $blogs[ $id ] ) ) {
			$cache = MslsSqlCacher::init( __CLASS__ )->set_params( __METHOD__ );

			// load post we need to exclude (already have translation) from search query
			$posts = $cache->get_results(
				$cache->prepare(
					"SELECT option_id, option_name FROM {$cache->options} WHERE option_name LIKE %s AND option_value LIKE %s",
					'msls_%',
					'%"' . $blogs[ $id ]->get_language() . '"%'
				)
			);

			$exclude_ids = array();
			foreach ( $posts as $post ) {
				$exclude_ids[] = substr( $post->option_name, 5 );
			}
			$query->query_vars['post__not_in'] = $exclude_ids;
			return $query;
		}
		return false;
	}

}
