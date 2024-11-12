<?php declare( strict_types = 1 );

namespace lloc\Msls\Component;

/**
 * Abstract class Input
 *
 * @package lloc\Msls\Component
 */
abstract class Component {

	const INPUT_PREFIX = 'msls_input_';

	const ALLOWED_HTML = array(
		'form'   => array(
			'action' => array(),
			'method' => array(),
		),
		'label'  => array(
			'for' => array(),
		),
		'option' => array(
			'value'    => array(),
			'selected' => array(),
		),
		'select' => array(
			'id'   => array(),
			'name' => array(),
		),
		'input'  => array(
			'type'     => array(),
			'class'    => array(),
			'id'       => array(),
			'name'     => array(),
			'value'    => array(),
			'size'     => array(),
			'readonly' => array(),
		),
	);

	/**
	 * @return string
	 */
	abstract public function render(): string;

	/**
	 * Adds our input elements to the allowed HTML elements of a post
	 */
	public static function get_allowed_html(): array {
		$my_allowed = wp_kses_allowed_html( 'post' );

		return array_merge( $my_allowed, self::ALLOWED_HTML );
	}
}
