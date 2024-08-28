<?php

namespace lloc\Msls;

class MslsContentFilter {

	protected MslsOptions $options;

	public function __construct( MslsOptions $options ) {
		$this->options = $options;
	}

	public static function init(): void {
		$obj = new self( msls_options() );

		add_filter( 'the_content', array( $obj, 'content_filter' ) );
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public function content_filter( string $content ) {
		if ( ! is_front_page() && is_singular() && $this->options->is_content_filter() ) {
			$content .= $this->filter_string();
		}

		return $content;
	}

	/**
	 * Create filter-string for msls_content_filter()
	 *
	 * @param string $pref
	 * @param string $post
	 *
	 * @return string
	 */
	public function filter_string( $pref = '<p id="msls">', $post = '</p>' ) {
		$links_arr = MslsOutput::create()->get( 1, true, true );
		$links_str = $this->format_available_languages( $links_arr );

		/* translators: %s: list of languages */
		$format = __( 'This post is also available in %s.', 'multisite-language-switcher' );

		if ( has_filter( 'msls_filter_string' ) ) {
			/**
			 * Overrides the string for the output of the translation hint
			 *
			 * @param string $output
			 * @param array $links
			 *
			 * @since 1.0
			 */
			$output = apply_filters( 'msls_filter_string', $format, $links_arr );
		} elseif ( $links_str ) {
			$output = sprintf( $format, $links_str );
		}

		return ! empty( $output ) ? $pref . $output . $post : '';
	}

	/**
	 * @param string[] $links
	 * @return string|null
	 */
	public function format_available_languages( array $links ): ?string {
		if ( empty( $links ) ) {
			return null;
		}

		if ( 1 == count( $links ) ) {
			return $links[0];
		}

		$last = array_pop( $links );

		/* translators: %1$s: list of languages separated by a comma, %2$s: last language */
		$format = __( '%1$s and %2$s', 'multisite-language-switcher' );

		return sprintf( $format, implode( ', ', $links ), $last );
	}
}
