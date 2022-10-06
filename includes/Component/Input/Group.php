<?php


namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\InputInterface;

/**
 * Class Options
 * @package lloc\Msls\Component\Input
 */
class Group implements InputInterface {

	/**
	 * @var string[]
	 */
	protected $arr = [];

	/**
	 * @var string
	 */
	protected $glue = '';

	/**
	 * Options constructor.
	 *
	 * @param string $glue
	 */
	public function __construct( string $glue = ' ' ) {
		$this->glue = $glue;
	}

	/**
	 * @param InputInterface $input
	 *
	 * @return self
	 */
	public function add( InputInterface $input ): self {
		$this->arr[] = $input;

		return $this;
	}

	/**
	 * @return string
	 */
	public function render(): string {
		$items = array_map( function ( InputInterface $input ) {
			return $input->render();
		}, $this->arr );

		return implode( $this->glue, $items );
	}

}