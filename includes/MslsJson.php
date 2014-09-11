<?php
/**
 * MslsJson
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.9
 */

/**
 * Container for an array which will used in JavaScript as object in JSON
 * @example https://gist.githubusercontent.com/lloc/2c232cef3f910acf692f/raw/1c4f62e1de57ca48f19c37e3a63e7dc311b76b2f/MslsJson.php
 * @package Msls
 */
class MslsJson {

	/**
	 * Container
	 * @var array
	 */
	protected $arr = array();

	/**
	 * add
	 * @param int $value
	 * @param string $label
	 * @return MslsJson
	 */
	public function add( $value, $label ) {
		$this->arr[] = array(
			'value' => (int) $value,
			'label' => (string) $label,
		);
		return $this;
	}

	/**
	 * compare
	 *
	 * Compare the item with the key "label" of the array $a and the
	 * array $b
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	public static function compare( array $a, array $b ) {
		return strnatcmp( $a['label'], $b['label'] );
	}

	/**
	 * get
	 *
	 * Get the array container sorted by label
	 * @return array
	 */
	public function get() {
		$arr = $this->arr;
		usort( $arr, array( __CLASS__, 'compare' ) );
		return $arr;
	}

	/**
	 * encode
	 *
	 * Encodes object and returns it as a json-string
	 * @return string
	 */
	public function encode() {
		return json_encode( $this->get() );
	}

	/**
	 * __toString
	 *
	 * Return the encoded object as a string using the encode-method
	 * @uses encode
	 * @return string
	 */
	public function __toString() {
		return $this->encode();
	}

}
