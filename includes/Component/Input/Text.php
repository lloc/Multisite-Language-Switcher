<?php


namespace lloc\Msls\Component\Input;


use lloc\Msls\Component\InputInterface;

class Text implements InputInterface {

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
	protected $size;

	/**
	 * @var string
	 */
	protected $readonly;

	/**
	 * @param string $key
	 * @param string|null $value
	 * @param string $size
	 * @param bool $readonly
	 */
	public function __construct( string $key, $value, string $size = '30', bool $readonly = false ) {
		$this->key      = esc_attr( $key );
		$this->value    = esc_attr( $value );
		$this->size     = esc_attr( $size );
		$this->readonly = $readonly ? ' readonly="readonly"' : '';
	}

	/**
	 * @return string
	 */
	public function render(): string {
		return sprintf(
			'<input type="text" class="regular-text" id="%1$s" name="msls[%1$s]" value="%2$s" size="%3$s"%4$s/>',
			$this->key,
			$this->value,
			$this->size,
			$this->readonly
		);

	}

}