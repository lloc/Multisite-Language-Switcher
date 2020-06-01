<?php

namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\InputInterface;

/**
 * Class Option
 * @package lloc\Msls\Component\Input
 */
class Option implements InputInterface {

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $selected;

	/**
	 * @param string $key
	 * @param string|null $value
	 * @param string|null $selected
	 */
	public function __construct( string $key, $value, $selected = null ) {
		$this->key      = esc_attr( $key );
		$this->value    = esc_attr( $value );
		$this->selected = selected( $key, esc_attr( $selected ), false );
	}

	/**
	 * @return string
	 */
	public function render(): string {
		return sprintf( '<option value="%1$s" %2$s>%3$s</option>', $this->key, $this->selected, $this->value );
	}

}