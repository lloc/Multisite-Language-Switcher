<?php declare( strict_types=1 );

namespace lloc\Msls\Options\Post;

use lloc\Msls\Options\Options;

/**
 * Post options
 *
 * @package Msls
 */
class Post extends Options {

	public const SEPARATOR = '_';

	/**
	 * @var bool
	 */
	protected bool $autoload = false;

	/**
	 * Get postlink
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_postlink( $language ) {
		if ( ! $this->has_value( $language ) ) {
			return '';
		}

		$post = get_post( (int) $this->__get( $language ) );
		if ( is_null( $post ) || 'publish' !== $post->post_status ) {
			return '';
		}

		if ( is_null( $this->with_front ) ) {
			$post_object      = get_post_type_object( $post->post_type );
			$this->with_front = ! empty( $post_object->rewrite['with_front'] );
		}

		$post_link = get_permalink( $post );

		$post_link = apply_filters_deprecated( 'check_url', array( $post_link, $this ), '2.7.1', Options::MSLS_GET_POSTLINK_HOOK );

		return apply_filters( Options::MSLS_GET_POSTLINK_HOOK, $post_link, $this ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- constant value is already prefixed with "msls_".
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link(): string {
		return (string) get_permalink( $this->get_arg( 0, 0 ) );
	}

	/**
	 * Gets the number of pages this blog has for the current request
	 *
	 * @param string $language
	 * @param string $context
	 *
	 * @return int
	 */
	public function get_max_pages( string $language, string $context = self::PAGINATION_ARCHIVE ): int {
		if ( self::PAGINATION_ARCHIVE === $context ) {
			return is_home() ? self::posts_to_pages( self::count_published( 'post' ) ) : 0;
		}

		$post = get_post( $this->get_post_id( $language ) );

		return is_null( $post ) ? 0 : substr_count( $post->post_content, '<!--nextpage-->' ) + 1;
	}

	/**
	 * Gets the post this options object builds a link for in the current blog
	 *
	 * @param string $language
	 *
	 * @return int
	 */
	protected function get_post_id( string $language ): int {
		return $this->has_value( $language ) ? (int) $this->__get( $language ) : $this->get_arg( 0, 0 );
	}
}
