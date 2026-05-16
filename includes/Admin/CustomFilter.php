<?php declare( strict_types=1 );

namespace lloc\Msls\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use lloc\Msls\Component\Input\Select;
use lloc\Msls\Db\Query\TranslatedPostIdQuery;
use lloc\Msls\Db\SqlCacher;
use lloc\Msls\Request\Fields;
use lloc\Msls\RestApi\Request;

/**
 * Adding custom filter to posts/pages table.
 *
 * @package Msls
 */
final class CustomFilter extends Main {

	/**
	 * @codeCoverageIgnore
	 */
	public static function init(): void {
		$options    = msls_options();
		$collection = msls_blog_collection();
		$obj        = new self( $options, $collection );

		if ( ! $options->is_excluded() ) {
			$post_type = msls_post_type()->get_request();
			if ( ! empty( $post_type ) ) {
				add_action( 'restrict_manage_posts', array( $obj, 'add_filter' ) );
				add_filter( 'parse_query', array( $obj, 'execute_filter' ) );
				add_filter(
					Select::RENDER_FILTER,
					function () {
						return Fields::FIELD_MSLS_FILTER;
					}
				);
			}
		}
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

			$id = Request::get( Fields::FIELD_MSLS_FILTER, 0 );

            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ( new Select( Fields::FIELD_MSLS_FILTER, $options, strval( $id ) ) )->render();
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
		if ( ! Request::has_var( Fields::FIELD_MSLS_FILTER ) ) {
			return false;
		}

		$id   = Request::get_var( Fields::FIELD_MSLS_FILTER );
		$blog = $this->collection->get_object( intval( $id ) );
		if ( ! $blog ) {
			return false;
		}

		$sql_cache = SqlCacher::create( __CLASS__, __METHOD__ );

		// Load post we need to exclude (they already have a translation) from search query.
		$query->query_vars['post__not_in'] = ( new TranslatedPostIdQuery( $sql_cache ) )( $blog->get_language() );

		return $query;
	}
}
