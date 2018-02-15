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
	 * @var string
	 */
	public $source_lang;

	/**
	 * @var string
	 */
	public $dest_lang;
}