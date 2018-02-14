<?php
/**
 * Created by PhpStorm.
 * User: luca
 * Date: 14/02/2018
 * Time: 10:11
 */

namespace lloc\Msls\ContentImport;


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
	 * ImportCoordinates constructor.
	 *
	 * @param int $source_blog_id
	 * @param int $source_post_id
	 * @param int $dest_blog_id
	 * @param int $dest_post_id
	 */
	public function __construct( $source_blog_id, $source_post_id, $dest_blog_id, $dest_post_id ) {
		$this->source_blog_id = $source_blog_id;
		$this->source_post_id = $source_post_id;
		$this->dest_blog_id = $dest_blog_id;
		$this->dest_post_id = $dest_post_id;
	}
}