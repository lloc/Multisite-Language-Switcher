<?php
/**
 * MslsOptionsPost
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Post options
 * @package Msls
 */
class MslsOptionsPost extends MslsOptions {

	/**
	 * Separator
	 * @var string
	 */
	protected $sep = '_';

	/**
	 * Autoload
	 * @var string
	 */
	protected $autoload = 'no';

	/**
	 * Get postlink
	 * @param string $language
	 * @return string
	 */
	public function get_postlink( $language ) {
		$url = '';

		if ( $this->has_value( $language ) ) {
			$post = get_post( (int) $this->__get( $language ) );

			if ( $post && 'publish' != $post->post_status ) {
				if ( is_null( $this->with_front ) ) {
					$post_object      = get_post_type_object( $post->post_type );
					$this->with_front = ! empty( $post_object->rewrite['with_front'] );
				}

				$url = get_permalink( $post );
			}
		}

		if ( has_filter( 'check_url' ) ) {
			// TODO: needs _deprecated_filter(), use _deprecated_function() as substitute for now
			_deprecated_function( 'check_url( $url, $this )', '1.0.9', 'MslsOption::get_postlink( $url, $this, $language )' );
			$url = apply_filters( 'check_url', $url, $this );
		}

		/**
		 * Filter postlink url
		 *
		 * __METHOD__ === 'MslsOptionsPost::get_postlink'
		 *
		 * @since 1.0.9
		 *
		 * @param string $url
		 * @param MslsOptions $this
		 * @param string $language
		 */
		return apply_filters( __METHOD__, $url, $this, $language );
	}

	/**
	 * Get current link
	 * @return string
	 */
	public function get_current_link() {
		return (string) get_permalink( $this->get_arg( 0, 0 ) );
	}

}
