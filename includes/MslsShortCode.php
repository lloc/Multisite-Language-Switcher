<?php

namespace lloc\Msls;

class MslsShortCode {


	protected MslsOptions $options;

	public function __construct( MslsOptions $options ) {
		$this->options = $options;
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function init(): void {
		$obj = new self( msls_options() );
		add_shortcode( 'sc_msls_widget', array( $obj, 'block_render' ) );
	}

	/**
	 * Renders output using the widget's output
	 *
	 * @return string|false
	 */
	public function block_render() {
		if ( $this->options->is_excluded() ) {
			return '';
		}

		ob_start();
		the_widget( MslsWidget::class );
		$output = ob_get_clean();

		return $output;
	}
}
