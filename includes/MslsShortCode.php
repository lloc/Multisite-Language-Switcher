<?php declare( strict_types=1 );

namespace lloc\Msls;

class MslsShortCode {

	public static function init(): void {
		add_shortcode( 'sc_msls_widget', array( __CLASS__, 'render_widget' ) );
		add_shortcode( 'sc_msls', 'msls_get_switcher' );
	}

	/**
	 * Renders output using the widget's output
	 *
	 * @return string
	 */
	public static function render_widget(): string {
		if ( msls_options()->is_excluded() ) {
			return '';
		}

		ob_start();
		the_widget( MslsWidget::class );

		return (string) ob_get_clean();
	}
}
