<?php declare( strict_types = 1 );

namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\Component;

final class Text extends Component {

	const DEFAULT_SIZE = 30;

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var int
	 */
	protected $size;

	/**
	 * @var string
	 */
	protected $readonly;

	/**
	 * @param string      $key
	 * @param string|null $value
	 * @param int         $size
	 * @param bool        $readonly
	 */
	public function __construct( string $key, ?string $value, int $size = self::DEFAULT_SIZE, bool $readonly = false ) {
		$this->key      = $key;
		$this->value    = $value;
		$this->size     = $size;
		$this->readonly = $readonly ? ' readonly="readonly"' : '';
	}

	/**
	 * @return string
	 */
	public function render(): string {
		return sprintf(
			'<input type="text" class="regular-text" id="%1$s" name="msls[%1$s]" value="%2$s" size="%3$d"%4$s/>',
			esc_attr( $this->key ),
			esc_attr( $this->value ),
			$this->size,
			$this->readonly // phpcs:ignore WordPress.Security.EscapeOutput
		);
	}
}
