<?php declare( strict_types=1 );

namespace lloc\Msls;

class MslsShortCode {

	public static function init(): void {
		add_shortcode( 'sc_msls_widget', \Closure::fromCallable( array( __CLASS__, 'render_widget' ) ) );
		add_shortcode( 'sc_msls', 'msls_get_switcher' );
	}

	/**
	 * Renders output using the widget's output
	 *
	 * @param array<string>|string $atts
	 * @param string|null          $content
	 * @param string               $tag
	 *
	 * @return string
	 */
	public static function render_widget( $atts = array(), ?string $content = null, string $tag = '' ): string {
		if ( msls_options()->is_excluded() ) {
			return '';
		}

		ob_start();
		the_widget( MslsWidget::class );
		$output = ob_get_clean();

		return false === $output ? '' : $output;
	}
}
