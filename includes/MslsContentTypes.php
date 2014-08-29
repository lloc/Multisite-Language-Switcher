<?php
/**
 * MslsContentTypes
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Supported content types
 * @package Msls
 */
class MslsContentTypes {

	/**
	 * Request
	 * @var string
	 */
	protected $request;

	/**
	 * Types
	 * @var array
	 */
	protected $types = array();

	/**
	 * Factory method
	 * @return MslsContentTypes
	 */
	public static function create() {
		$_request = MslsPlugin::get_superglobals( array( 'taxonomy' ) );
		if ( '' != $_request['taxonomy'] ) {
			return MslsTaxonomy::instance();
		}
		return MslsPostType::instance();
	}

	/**
	 * Check for post_type
	 * @return bool
	 */
	public function is_post_type() {
		return false;
	}

	/**
	 * Check for taxonomy
	 * @return bool
	 */
	public function is_taxonomy() {
		return false;
	}

	/**
	 * Check if the current user can manage this content type
	 *
	 * Returns name of the content type if the user has access or an empty
	 * string if the user can not access
	 * @return string
	 */
	public function acl_request() {
		return '';
	}

	/**
	 * Getter
	 * @return array
	 */
	public function get() {
		return (array) $this->types;
	}

	/**
	 * Gets the request if it is an allowed content type
	 * @return string
	 */
	public function get_request() {
		return(
			in_array( $this->request, $this->types ) ?
			$this->request :
			''
		);
	}

}
