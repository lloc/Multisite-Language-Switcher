<?php declare( strict_types = 1 );

namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\Component;

final class Select extends Component {

	const RENDER_FILTER = 'msls_input_select_name';

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var Group
	 */
	protected $options;

	/**
	 * @param string  $key Name and ID of the form-element
	 * @param mixed[] $arr Options as associative array
	 * @param ?string $selected Values which should be selected
	 */
	public function __construct( string $key, array $arr, ?string $selected = null ) {
		$this->key = $key;

		$this->options = new Group( '' );
		foreach ( $arr as $key => $value ) {
			$this->options->add( new Option( strval( $key ), strval( $value ), $selected ) );
		}
	}

	/**
	 * @return string
	 */
	public function render(): string {
		$name = apply_filters( self::RENDER_FILTER, 'msls[' . $this->key . ']' );

		return sprintf(
			'<select id="%1$s" name="%2$s">%3$s</select>',
			esc_attr( $this->key ),
			esc_attr( $name ),
			$this->options->render() // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}
}
