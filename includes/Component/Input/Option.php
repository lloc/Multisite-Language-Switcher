<?php declare( strict_types = 1 );

namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\Component;

/**
 * Class Option
 *
 * @package lloc\Msls\Component\Input
 */
final class Option extends Component {

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
	 * @param string  $key
	 * @param string  $value
	 * @param ?string $selected
	 */
	public function __construct( string $key, string $value, ?string $selected = null ) {
		$this->key      = $key;
		$this->value    = $value;
		$this->selected = selected( $key, $selected, false );
	}

	/**
	 * @return string
	 */
	public function render(): string {
		return sprintf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $this->key ),
			esc_attr( $this->selected ),
			esc_html( $this->value )
		);
	}
}
