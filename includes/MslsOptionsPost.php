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
	 *
	 * @param string $language
	 * @param string $url
	 *
	 * @return string
	 */
	public function get_postlink( $language, $url = '' ) {
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

		return parent::get_postlink( $language, $url );
	}

	/**
	 * Get current link
	 * @return string
	 */
	public function get_current_link() {
		return (string) get_permalink( $this->get_arg( 0, 0 ) );
	}

}
