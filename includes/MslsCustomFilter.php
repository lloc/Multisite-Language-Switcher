<?php

namespace lloc\Msls;

use lloc\Msls\Component\Input\Select;
use lloc\Msls\Query\TranslatedPostIdQuery;

/**
 * Adding custom filter to posts/pages table.
 *
 * @package Msls
 */
class MslsCustomFilter extends MslsMain {

	const FILTER_NAME = 'msls_filter';

	/**
	 * Init
	 *
	 * @codeCoverageIgnore
	 *
	 * @return MslsCustomFilter
	 */
	public static function init() {
		$options    = msls_options();
		$collection = msls_blog_collection();
		$obj        = new static( $options, $collection );

		if ( ! $options->is_excluded() ) {
			$post_type = MslsPostType::instance()->get_request();
			if ( ! empty( $post_type ) ) {
				add_action( 'restrict_manage_posts', array( $obj, 'add_filter' ) );
				add_filter( 'parse_query', array( $obj, 'execute_filter' ) );
				add_filter(
					'msls_input_select_name',
					function () {
						return self::FILTER_NAME;
					}
				);
			}
		}

		return $obj;
	}

	/**
	 * Echo's select tag with list of blogs
	 *
	 * @uses selected
	 */
	public function add_filter(): void {
		$id = (
			filter_has_var( INPUT_GET, self::FILTER_NAME ) ?
			filter_input( INPUT_GET, self::FILTER_NAME, FILTER_SANITIZE_NUMBER_INT ) :
			'0'
		);

		$blogs = $this->collection->get();
		if ( $blogs ) {
			$options = array( '' => esc_html( __( 'Show all posts', 'multisite-language-switcher' ) ) );
			foreach ( $blogs as $blog ) {
				$options[ strval( $blog->userblog_id ) ] = sprintf(
					__( 'Not translated in the %s-blog', 'multisite-language-switcher' ),
					$blog->get_description()
				);
			}

			echo ( new Select( self::FILTER_NAME, $options, $id ) )->render();
		}
	}

	/**
	 * Executes filter, excludes translated posts from WP_Query
	 *
	 * @param \WP_Query $query
	 *
	 * @return bool|\WP_Query
	 */
	public function execute_filter( \WP_Query $query ) {
		if ( ! filter_has_var( INPUT_GET, self::FILTER_NAME ) ) {
			return false;
		}

		$id   = filter_input( INPUT_GET, self::FILTER_NAME, FILTER_SANITIZE_NUMBER_INT );
		$blog = $this->collection->get_object( intval( $id ) );

		if ( $blog ) {
			$sql_cache = MslsSqlCacher::create( __CLASS__, __METHOD__ );

			// load post we need to exclude (they already have a translation) from search query
			$query->query_vars['post__not_in'] = ( new TranslatedPostIdQuery( $sql_cache ) )( $blog->get_language() );

			return $query;
		}

		return false;
	}
}
