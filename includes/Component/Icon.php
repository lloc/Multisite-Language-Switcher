<?php declare( strict_types = 1 );

namespace lloc\Msls\Component;

/**
 * Class Icon
 *
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
		$file_path = $this->get_include();

		$this->map = ! is_null( $file_path ) && is_readable( $file_path ) ? require $file_path : array();
	}

	/**
	 * @param string $language
	 * @param string $prefix
	 * @param string $postfix
	 *
	 * @return string
	 */
	protected function maybe( string $language, string $prefix = '', string $postfix = '' ): string {
		if ( 5 === strlen( $language ) ) {
			$language = strtolower( substr( $language, - 2 ) );
		}

		return $prefix . $language . $postfix;
	}

	/**
	 * @return ?string
	 */
	protected function get_include(): ?string {
		return null;
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	abstract public function get( string $language ): string;
}
