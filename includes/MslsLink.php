<?php
/**
 * MslsLink
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

/**
 * Link type: Image and text
 * @package Msls
 * @property string $txt
 * @property string $src
 * @property string $alt
 * @property string $url
 */
class MslsLink extends MslsGetSet {

	/**
	 * Output format
	 * @var string
	 */
	protected $format_string = '<img src="{src}" alt="{alt}"/> {txt}';

	/**
	 * Gets all link types as array with "id => name"-items
	 *
	 * @return string[]
	 */
	public static function get_types() {
		return [
			MslsLink::class,
			MslsLinkTextOnly::class,
			MslsLinkImageOnly::class,
			MslsLinkTextImage::class,
		];
	}

	/**
	 * Gets the link description.
	 *
	 * @return string
	 */
	public static function get_description() {
		return __( 'Flag and description', 'multisite-language-switcher' );
	}

	/**
	 * Gets an array with all link descriptions
	 *
	 * @return array
	 */
	public static function get_types_description() {
		$types = [];

		foreach ( self::get_types() as $key => $class ) {
			$types[ $key ] = call_user_func( [ $class, 'get_description' ] );
		}

		return $types;
	}

	/**
	 * Factory: Creates a specific instance of MslsLink
	 *
	 * @codeCoverageIgnore
	 *
	 * @param int $display
	 *
	 * @return MslsLink
	 */
	public static function create( $display ) {
		if ( has_filter( 'msls_link_create' ) ) {
			/**
			 * Returns custom MslsLink-Object
			 *
			 * @param int $display
			 *
			 * @return MslsLink
			 * @since 0.9.9
			 *
			 */
			$obj = apply_filters( 'msls_link_create', $display );
			if ( is_subclass_of( $obj, __CLASS__ ) ) {
				return $obj;
			}
		}

		$types = self::get_types();
		if ( ! in_array( $display, array_keys( $types ), true ) ) {
			$display = 0;
		}

		return new $types[ $display ];
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
	 *
	 * @return string
	 */
	public function __toString() {
		$temp = $this->get_arr();

		return str_replace(
			array_map( [ $this, 'callback' ], array_keys( $temp ) ),
			$temp,
			$this->format_string
		);
	}

}
