<?php

namespace lloc\Msls;

class MslsShortCode {

	public static function init(): void {
		add_shortcode( 'sc_msls_widget', array( __CLASS__, 'render_widget' ) );
		add_shortcode( 'sc_msls', 'get_the_msls' );
	}

	/**
	 * Renders output using the widget's output
	 *
	 * @return string|false
	 */
	public static function render_widget() {
		if ( msls_options()->is_excluded() ) {
			return '';
		}

		ob_start();
		the_widget( MslsWidget::class );
		$output = ob_get_clean();

		return $output;
	}
}
