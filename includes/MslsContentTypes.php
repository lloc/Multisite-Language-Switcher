<?php

namespace lloc\Msls;

/**
 * Supported content types
 *
 * @package Msls
 */
abstract class MslsContentTypes extends MslsRegistryInstance {

	/**
	 * Request
	 *
	 * @var string
	 */
	protected string $request;

	/**
	 * Types
	 *
	 * @var string[]
	 */
	protected array $types = array();

	/**
	 * Factory method
	 *
	 * @return MslsContentTypes
	 */
	public static function create() {
		$_request = MslsRequest::get_request( array( 'taxonomy' ) );

		return '' != $_request['taxonomy'] ? MslsTaxonomy::instance() : MslsPostType::instance();
	}

	/**
	 * Check for post_type
	 *
	 * @return bool
	 */
	public function is_post_type() {
		return false;
	}

	/**
	 * Check for taxonomy
	 *
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
	 *
	 * @return string
	 */
	public function acl_request() {
		return '';
	}

	/**
	 * Getter
	 *
	 * @return string[]
	 */
	abstract public static function get(): array;

	/**
	 * Gets the request if it is an allowed content type
	 *
	 * @return string
	 */
	abstract public function get_request(): string;
}
