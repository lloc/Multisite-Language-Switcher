<?php
/**
 * MslsJson
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.9
 */

namespace lloc\Msls;

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
	protected array $arr = [];

	/** MslsLanguageArray
	 * Adds a value label pair to the internal class container
	 *
	 * @param mixed $value
	 * @param mixed $label
	 *
	 * @return MslsJson
	 */
	public function add( $value, $label ) {
		$this->arr[] = [
			'value' => intval( $value ),
			'label' => strval( $label ),
		];

		return $this;
	}

	/**
	 * Compare the item with the key "label" of the array $a and the array $b
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	public static function compare( array $a, array $b ) {
		return strnatcmp( $a['label'], $b['label'] );
	}

	/**
	 * Get the array container sorted by label
	 *
	 * @return array
	 */
	public function get(): array {
		$arr = $this->arr;

		usort( $arr, [ __CLASS__, 'compare' ] );

		return $arr;
	}

	/**
	 * Encodes object and returns it as a json-string
	 *
	 * @return string
	 */
	public function encode(): string {
		return json_encode( $this->get() );
	}

	/**
	 * Return the encoded object as a string using the encode-method
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->encode();
	}

}
