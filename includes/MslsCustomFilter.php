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
						return MslsFields::FIELD_MSLS_FILTER;
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
		$blogs = $this->collection->get();
		if ( $blogs ) {
			$options = array( '' => esc_html( __( 'Show all posts', 'multisite-language-switcher' ) ) );
			foreach ( $blogs as $blog ) {
				/* translators: %s: blog name */
				$format = __( 'Not translated in the %s-blog', 'multisite-language-switcher' );

				$options[ strval( $blog->userblog_id ) ] = sprintf( $format, $blog->get_description() );
			}

			$id = MslsRequest::get( MslsFields::FIELD_MSLS_FILTER, 0 );

			echo ( new Select( MslsFields::FIELD_MSLS_FILTER, $options, $id ) )->render();
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
		if ( ! MslsRequest::has_var( MslsFields::FIELD_MSLS_FILTER ) ) {
			return false;
		}

		$id   = MslsRequest::get_var( MslsFields::FIELD_MSLS_FILTER );
		$blog = $this->collection->get_object( intval( $id ) );
		if ( ! $blog ) {
			return false;
		}

		$sql_cache = MslsSqlCacher::create( __CLASS__, __METHOD__ );

		// load post we need to exclude (they already have a translation) from search query
		$query->query_vars['post__not_in'] = ( new TranslatedPostIdQuery( $sql_cache ) )( $blog->get_language() );

		return $query;
	}
}
