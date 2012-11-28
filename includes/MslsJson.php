<?php
/**
 * MslsJson
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.9
 */

/**
 * Container for an array which will used in JavaScript as JSON object
 * @package Msls
 * @subpackage Main
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
	 * __toString
	 * 
	 * Return the array container as a JSON string when the object will
	 * be converted to a string
	 * @return string
	 */ 
	public function __toString() {
		return json_encode( $this->get() );
	}

}
