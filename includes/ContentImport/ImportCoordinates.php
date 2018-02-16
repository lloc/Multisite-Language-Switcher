<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\MslsBlogCollection;

class ImportCoordinates {

	/**
	 * @var int
	 */
	public $source_blog_id;
	/**
	 * @var int
	 */
	public $source_post_id;
	/**
	 * @var int
	 */
	public $dest_blog_id;
	/**
	 * @var int
	 */
	public $dest_post_id;

	/**
	 * @var \WP_Post
	 */
	public $source_post;

	/**
	 * @var string
	 */
	public $source_lang;

	/**
	 * @var string
	 */
	public $dest_lang;

	/**
	 * Validates the coordinates.
	 *
	 * @return bool
	 */
	public function validate() {
		if ( ! get_blog_post( $this->source_blog_id, $this->source_post_id ) ) {
			return false;
		}
		if ( ! get_blog_post( $this->dest_blog_id, $this->dest_post_id ) ) {
			return false;
		}
		if ( ! $this->source_post instanceof \WP_Post ) {
			return false;
		}

		if ( $this->source_lang !== MslsBlogCollection::get_blog_language( $this->source_blog_id ) ) {
			return false;
		}
		if ( $this->dest_lang !== MslsBlogCollection::get_blog_language( $this->dest_blog_id ) ) {
			return false;
		}

		return true;
	}
}