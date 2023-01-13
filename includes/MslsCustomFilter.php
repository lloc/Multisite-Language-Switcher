<?php

namespace lloc\Msls;

use WP_Query;

/**
 * Adding custom filter to posts/pages table.
 *
 * @package Msls
 */
class MslsCustomFilter extends MslsMain implements HookInterface {

	/**
	 * Init
	 *
	 * @codeCoverageIgnore
	 *
	 * @return HookInterface
	 */
	public static function init(): HookInterface {
		$options    = MslsOptions::instance();
		$collection = MslsBlogCollection::instance();
		$obj        = new self( $options, $collection );

		if ( ! $options->is_excluded() ) {
			$post_type = MslsPostType::instance()->get_request();
			if ( ! empty( $post_type ) ) {
				add_action( 'restrict_manage_posts', [ $obj, 'add_filter' ] );
				add_filter( 'parse_query', [ $obj, 'execute_filter' ] );
			}
		}

		return $obj;
	}

	/**
	 * Echo's select tag with list of blogs
	 *
	 * @return void
	 */
	public function add_filter(): void {
		$id = '';

		if ( filter_has_var( INPUT_GET, 'msls_filter' ) ) {
			$id = filter_input( INPUT_GET, 'msls_filter', FILTER_SANITIZE_NUMBER_INT );
		}

		$blogs = $this->collection->get();
		if ( $blogs ) {
			echo '<select name="msls_filter" id="msls_filter">';
			echo '<option value="">' . esc_html( __( 'Show all blogs', 'multisite-language-switcher' ) ) . '</option>';

			foreach ( $blogs as $blog ) {
				$label = sprintf( __( 'Not translated in the %s-blog', 'multisite-language-switcher' ), $blog->get_description() );

				printf(
					'<option value="%d" %s>%s</option>',
					$blog->userblog_id,
					selected( $id, $blog->userblog_id, false ),
					$label
				);
			}

			echo '</select>';
		}
	}

	/**
	 * Executes filter, excludes translated posts from WP_Query
	 *
	 * @param WP_Query $query
	 *
	 * @return bool|WP_Query
	 */
	public function execute_filter( WP_Query $query ) {
		if ( ! filter_has_var( INPUT_GET, 'msls_filter' ) ) {
			return false;
		}

		$id = filter_input( INPUT_GET, 'msls_filter', FILTER_SANITIZE_NUMBER_INT );

		$blogs = $this->collection->get();
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

			$exclude_ids = [];
			foreach ( $posts as $post ) {
				$exclude_ids[] = substr( $post->option_name, 5 );
			}
			$query->query_vars['post__not_in'] = $exclude_ids;

			return $query;
		}

		return false;
	}

}
