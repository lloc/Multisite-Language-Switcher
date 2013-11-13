<?php
/**
 * MslsLink
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Link type: Image and text
 * 
 * @package Msls
 */
class MslsLink extends MslsGetSet {

	/**
	 * Output format
	 * 
	 * @var string
	 */
	protected $format_string = '<img src="{src}" alt="{alt}"/> {txt}';

	/**
	 * Gets all link types as array with "id => name"-items.
	 * 
	 * @return array
	 */
	public static function get_types() {
		return array( 
			'0' => 'MslsLink',
			'1' => 'MslsLinkTextOnly',
			'2' => 'MslsLinkImageOnly',
			'3' => 'MslsLinkTextImage',
		);
	}

	/**
	 * Gets the link description.
	 * 
	 * @return string
	 */
	public static function get_description() {
		return __( 'Flag and description', 'msls' );
	}

	/**
	 * Gets an array with all link descriptions.
	 * 
	 * @return array
	 */
	public static function get_types_description() {
		$temp  = array();
		foreach ( self::get_types() as $key => $class ) {
			$temp[$key] = call_user_func(
				array( $class, 'get_description' )
			);
		}
		return $temp;
	}

	/**
	 * Factory: Creates a specific instance of MslsLink.
	 * 
	 * @param int $display
	 * @return MslsLink
	 */
	public static function create( $display ) {
		if ( has_filter( 'msls_link_create' ) ) {
			/**
			 * Lets you create your own MslsLink-Object
			 * @param int $display
			 */
			$obj = apply_filters( 'msls_link_create', $display );
			if ( is_subclass_of( $obj, 'MslsLink' ) )
				return $obj;
		}
		$types = self::get_types();
		if ( ! in_array( $display, array_keys( $types ), true ) )
			$display = 0;
		return new $types[$display];
	}

	/**
	 * Handles the request to print the object
	 */
	public function __toString() {
		$temp = $this->get_arr();
		return str_replace(
			array_map(
				create_function( '$x', 'return "{" . $x . "}";' ),
				array_keys( $temp )
			),
			$temp,
			$this->format_string
		);
	}

}
