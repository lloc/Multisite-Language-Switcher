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
		add_shortcode( 'sc_msls_widget', array( $obj, 'render_widget' ) );
		add_shortcode( 'sc_msls', 'get_the_msls' );
	}

	/**
	 * Renders output using the widget's output
	 *
	 * @return string|false
	 */
	public function render_widget() {
		if ( $this->options->is_excluded() ) {
			return '';
		}

		ob_start();
		the_widget( MslsWidget::class );
		$output = ob_get_clean();

		return $output;
	}
}
