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
			'action' => true,
			'method' => true,
		),
		'label'  => array(
			'for' => true,
		),
		'option' => array(
			'value'    => true,
			'selected' => true,
		),
		'select' => array(
			'id'   => true,
			'name' => true,
		),
		'input'  => array(
			'type'     => true,
			'class'    => true,
			'id'       => true,
			'name'     => true,
			'value'    => true,
			'size'     => true,
			'readonly' => true,
		),
	);

	/**
	 * @return string
	 */
	abstract public function render(): string;

	/**
	 * Adds our input elements to the allowed HTML elements of a post
	 *
	 * @return array<string, array<string, bool>>
	 */
	public static function get_allowed_html(): array {
		$my_allowed = wp_kses_allowed_html( 'post' );

		return array_merge( $my_allowed, self::ALLOWED_HTML );
	}
}
