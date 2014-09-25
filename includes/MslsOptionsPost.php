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
		if ( $this->has_value( $language ) ) {
			$post = get_post( (int) $this->__get( $language ) );
			if ( ! is_null( $post ) && 'publish' == $post->post_status ) {
				return get_permalink( $post );
			}
		}
		return '';
	}

	/**
	 * Get current link
	 * @return string
	 */
	public function get_current_link() {
		return (string) get_permalink( $this->get_arg( 0, 0 ) );
	}

}
