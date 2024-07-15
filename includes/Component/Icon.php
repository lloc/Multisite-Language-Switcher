<?php

namespace lloc\Msls\Component;

/**
 * Class Icon
 * @package lloc\Msls\Component
 */
abstract class Icon {

	/**
	 * @var string[]
	 */
	protected $map;

	/**
	 * Icon constructor
	 */
	public function __construct() {
		if ( $this->get_include() ) {
			$this->map = include $this->get_include();
		}
	}

	/**
	 * @param string $language
	 * @param string $prefix
	 * @param string $postfix
	 *
	 * @return string
	 */
	protected function maybe( string $language, string $prefix = '', string $postfix = '' ): string {
		if ( 5 == strlen( $language ) ) {
			$language = strtolower( substr( $language, - 2 ) );
		}

		return $prefix . $language . $postfix;
	}

	/**
	 * @return string
	 */
	abstract protected function get_include(): string;

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	abstract public function get( string $language ): string;

}