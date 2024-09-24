<?php declare( strict_types = 1 );

namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\Component;

/**
 * Class Checkbox
 *
 * @package lloc\Msls\Component\Input
 */
final class Checkbox extends Component {

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
		return sprintf(
			'<input type="checkbox" id="%1$s" name="msls[%1$s]" value="1" %2$s/>',
			esc_attr( $this->key ),
			$this->selected // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}
}
