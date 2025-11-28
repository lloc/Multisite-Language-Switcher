<?php declare( strict_types=1 );

namespace lloc\Msls;

use lloc\Msls\Map\HrefLang;

/**
 * Output in the frontend
 *
 * @package Msls
 */
class MslsOutput extends MslsMain {

	const MSLS_ALTERNATE_LINKS_HOOK = 'msls_output_get_alternate_links';

	const MSLS_ALTERNATE_LINKS_ARR_HOOK = 'msls_output_get_alternate_links_arr';

	const MSLS_ALTERNATE_LINKS_DEFAULT_HOOK = 'msls_output_get_alternate_links_default';

	const MSLS_GET_HOOK = 'msls_output_get';

	const MSLS_NO_TRANSLATION_FOUND_HOOK = 'msls_output_no_translation_found';

	const MSLS_GET_TAGS_HOOK = 'msls_output_get_tags';


	public static function init(): object {
		_deprecated_function( __METHOD__, '2.9.2', 'MslsOutput::create' );

		return self::create();
	}

	/**
	 * Holds the format for the output
	 *
	 * @var array<string, string>
	 */
	protected array $tags;

	/**
	 * @param ?int $display
	 * @param bool $filter
	 * @param bool $exists
	 *
	 * @return string[]
	 */
	public function get( ?int $display, bool $filter = false, $exists = false ): array {
		$arr = array();

		$blogs = $this->collection->get_filtered( $filter );
		if ( $blogs ) {
			$mydata = MslsOptions::create();
			$link   = MslsLink::create( $display );

			foreach ( $blogs as $blog ) {
				$language = $blog->get_language();

				$link->src = $this->options->get_flag_url( $language );
				$link->alt = $language;

				$is_current_blog = $this->collection->is_current_blog( $blog );
				if ( $is_current_blog ) {
					$url       = $mydata->get_current_link();
					$link->txt = $blog->get_description();
				} else {
					switch_to_blog( $blog->userblog_id );

					if ( $this->is_requirements_not_fulfilled( $mydata, $exists, $language ) ) {
						restore_current_blog();
						continue;
					} else {
						$url       = $mydata->get_permalink( $language );
						$link->txt = $blog->get_description();
					}

					restore_current_blog();
				}

				if ( has_filter( self::MSLS_GET_HOOK ) ) {
					/**
					 * Returns HTML-link for an item of the output-arr
					 *
					 * @param string $url
					 * @param LinkInterface $link
					 * @param bool $is_current_blog
					 *
					 * @since 0.9.8
					 */
					$arr[] = (string) apply_filters( self::MSLS_GET_HOOK, $url, $link, $is_current_blog );
				} else {
					$arr[] = sprintf(
						'<a href="%s" title="%s"%s>%s</a>',
						$url,
						$link->txt,
						$is_current_blog ? ' class="current_language" aria-current="page"' : '',
						$link
					);
				}
			}
		}

		return $arr;
	}

	/**
	 * Get alternate links for the head section
	 *
	 * @return string
	 */
	public function get_alternate_links() {
		$blogs     = msls_blog_collection();
		$href_lang = new HrefLang( $blogs );
		$options   = MslsOptions::create();
		$arr       = array();
		$default   = '';

		foreach ( $blogs->get_objects() as $blog ) {
			$url = apply_filters( self::MSLS_ALTERNATE_LINKS_HOOK, $blog->get_url( $options ), $blog );
			if ( is_null( $url ) ) {
				continue;
			}

			$hreflang = $href_lang->get( $blog->get_language() );
			$format   = '<link rel="alternate" href="%1$s" hreflang="%2$s" />';

			if ( '' === $default ) {
				$default = sprintf( $format, esc_url( $url ), 'x-default' );
			}

			$arr[] = sprintf( $format, esc_url( $url ), esc_attr( $hreflang ) );
		}

		if ( 1 === count( $arr ) ) {
			return apply_filters( self::MSLS_ALTERNATE_LINKS_DEFAULT_HOOK, $default );
		}

		$arr = (array) apply_filters( self::MSLS_ALTERNATE_LINKS_ARR_HOOK, $arr );

		return implode( PHP_EOL, $arr );
	}

	/**
	 * Returns a string when the object will be treated like a string
	 *
	 * @return string
	 */
	public function __toString() {
		$arr = $this->get( $this->options->display, false, isset( $this->options->only_with_translation ) );
		if ( empty( $arr ) ) {
			return apply_filters( self::MSLS_NO_TRANSLATION_FOUND_HOOK, '' );
		}

		$tags = $this->get_tags();

		return $tags['before_output'] . $tags['before_item'] .
				implode( $tags['after_item'] . $tags['before_item'], $arr ) .
				$tags['after_item'] . $tags['after_output'];
	}

	/**
	 * Gets tags for the output
	 *
	 * @return array<string, string>
	 */
	public function get_tags(): array {
		if ( empty( $this->tags ) ) {
			$this->tags = array(
				'before_item'   => $this->options->before_item,
				'after_item'    => $this->options->after_item,
				'before_output' => $this->options->before_output,
				'after_output'  => $this->options->after_output,
			);

			/**
			 * Returns tags array for the output
			 *
			 * @param array $tags
			 *
			 * @since 1.0
			 */
			$this->tags = (array) apply_filters( self::MSLS_GET_TAGS_HOOK, $this->tags );
		}

		return $this->tags;
	}

	/**
	 * Sets tags for the output
	 *
	 * @param string[] $arr
	 *
	 * @return MslsOutput
	 */
	public function set_tags( array $arr = array() ): MslsOutput {
		$this->tags = wp_parse_args( $this->get_tags(), $arr );

		return $this;
	}

	/**
	 * Returns true if the requirements not fulfilled
	 *
	 * @param ?OptionsInterface $thing
	 * @param boolean           $exists
	 * @param string            $language
	 *
	 * @return boolean
	 */
	public function is_requirements_not_fulfilled( $thing, $exists, $language ) {
		if ( is_null( $thing ) ) {
			return $exists;
		}

		return MslsOptions::class !== get_class( $thing ) && ! $thing->has_value( $language ) && $exists;
	}
}
