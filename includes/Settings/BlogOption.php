<?php

namespace lloc\Msls\Settings;


class BlogOption {

	/**
	 * @var mixed
	 */
	protected $option;

	/**
	 * @var string
	 */
	protected $option_name = 'msls';

	/**
	 * @var mixed
	 */
	protected $default = [];

	/**
	 * @param int $blog_id
	 */
	public function __construct( int $blog_id ) {
		$this->option = get_blog_option( $blog_id, $this->option_name, $this->default );
	}

}