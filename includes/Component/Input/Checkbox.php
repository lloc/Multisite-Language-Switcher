<?php

namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\InputInterface;

/**
 * Class Checkbox
 * @package lloc\Msls\Component\Input
 */
class Checkbox implements InputInterface {

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $selected;

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function __construct( string $key, ?string $value ) {
		$this->key      = esc_attr( $key );
		$this->selected = checked( 1, $value, false );
	}

	/**
	 * @return string
	 */
	public function render(): string {
		return sprintf( '<input type="checkbox" id="%1$s" name="msls[%1$s]" value="1" %2$s/>', $this->key, $this->selected );
	}

}