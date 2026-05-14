<?php declare( strict_types=1 );

namespace lloc\Msls\Link;

use lloc\Msls\LinkInterface;
use lloc\Msls\MslsGetSet;

/**
 * Link type: Image and text
 *
 * @package Msls
 */
class Link extends MslsGetSet implements LinkInterface {

	/**
	 * Output format
	 *
	 * @var string
	 */
	protected $format_string = '<img src="{src}" alt="{alt}"/> {txt}';

	/**
	 * Gets all link types as an array with "id => name"-items
	 *
	 * @return array<int, class-string<LinkInterface>>
	 */
	public static function get_types() {
		return array(
			self::class,
			TextOnly::class,
			ImageOnly::class,
			TextImage::class,
		);
	}

	/**
	 * Gets the link description.
	 *
	 * @return string
	 */
	public static function get_description(): string {
		return __( 'Flag and description', 'multisite-language-switcher' );
	}

	/**
	 * Gets an array with all link descriptions
	 *
	 * @return array<int, string>
	 */
	public static function get_types_description(): array {
		$types = array();

		foreach ( self::get_types() as $key => $class ) {
			$types[ $key ] = $class::get_description();
		}

		return $types;
	}

	/**
	 * Factory: Creates a specific instance of MslsLink
	 *
	 * @param ?int $display
	 *
	 * @return LinkInterface
	 */
	public static function create( ?int $display ): LinkInterface {
		$types = self::get_types();
		if ( ! in_array( $display, array_keys( $types ), true ) ) {
			$display = 0;
		}

		$obj = new $types[ $display ]();

		if ( has_filter( 'msls_link_create' ) ) {
			$filtered = apply_filters( 'msls_link_create', $obj, $display );
			if ( $filtered instanceof LinkInterface ) {
				$obj = $filtered;
			}
		}

		return $obj;
	}

	/**
	 * Callback function (no lambda here because PHP 5.2 might be still in use)
	 *
	 * @param mixed $x
	 *
	 * @return string
	 */
	public static function callback( $x ) {
		return '{' . $x . '}';
	}

	/**
	 * Handles the request to print the object
	 */
	public function __toString(): string {
		$temp = $this->get_arr();

		return str_replace(
			array_map( array( $this, 'callback' ), array_keys( $temp ) ),
			$temp,
			$this->format_string
		);
	}
}
