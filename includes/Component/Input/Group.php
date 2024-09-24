<?php declare( strict_types = 1 );

namespace lloc\Msls\Component\Input;

use lloc\Msls\Component\Component;

/**
 * Class Options
 *
 * @package lloc\Msls\Component\Input
 */
final class Group extends Component {

	/**
	 * @var Component[]
	 */
	protected $arr = array();

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
	 * @param Component $input
	 *
	 * @return self
	 */
	public function add( Component $input ): self {
		$this->arr[] = $input;

		return $this;
	}

	/**
	 * @return string
	 */
	public function render(): string {
		$items = array_map(
			function ( Component $input ) {
				return $input->render(); // phpcs:ignore WordPress.Security.EscapeOutput
			},
			$this->arr
		);

		return implode( $this->glue, $items );
	}
}
