<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * Post options
 *
 * @package Msls
 */
class MslsOptionsPost extends MslsOptions {

	/**
	 * Separator
	 *
	 * @var string
	 */
	protected $sep = '_';

	/**
	 * Autoload
	 *
	 * @var string
	 */
	protected $autoload = 'no';

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
		if ( is_null( $post ) || 'publish' != $post->post_status ) {
			return '';
		}

		if ( is_null( $this->with_front ) ) {
			$post_object      = get_post_type_object( $post->post_type );
			$this->with_front = ! empty( $post_object->rewrite['with_front'] );
		}

		$post_link = get_permalink( $post );

		$post_link = apply_filters_deprecated( 'check_url', array( $post_link, $this ), '2.7.1', 'msls_get_postlink' );

		return apply_filters( 'msls_get_postlink', $post_link, $this );
	}

	/**
	 * Get current link
	 *
	 * @return string
	 */
	public function get_current_link(): string {
		return (string) get_permalink( $this->get_arg( 0, 0 ) );
	}
}
