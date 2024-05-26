<?php

namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\InputInterface;

class Select implements InputInterface {

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var Group
	 */
	protected $options;

	/**
	 * @param string   $key Name and ID of the form-element
	 * @param string[] $arr Options as associative array
	 * @param ?string  $selected Values which should be selected
	 */
	public function __construct( string $key, array $arr, ?string $selected = null ) {
		$this->key = esc_attr( $key );

		$this->options = new Group( '' );
		foreach ( $arr as $key => $value ) {
			$this->options->add( new Option( $key, $value, $selected ) );
		}
	}

	/**
	 * @return string
	 */
	public function render(): string {
		$name = apply_filters( 'msls_input_select_name', 'msls[' . $this->key . ']' );

		return sprintf( '<select id="%1$s" name="%2$s">%3$s</select>', $this->key, $name, $this->options->render() );
	}
}
