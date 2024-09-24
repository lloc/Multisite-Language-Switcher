<?php declare( strict_types = 1 );

namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\Component;

/**
 * Class Label
 *
 * @package lloc\Msls\Component\Input
 */
final class Label extends Component {

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $text;

	/**
	 * @param string $key
	 * @param string $text
	 */
	public function __construct( string $key, string $text ) {
		$this->key  = esc_attr( $key );
		$this->text = esc_html( $text );
	}

	/**
	 * @return string
	 */
	public function render(): string {
		return sprintf( '<label for="%1$s">%2$s</label>', esc_html( $this->key ), esc_html( $this->text ) );
	}
}
