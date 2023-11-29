<?php
/**
 * MslsOutput
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

use lloc\Msls\Map\HrefLang;

/**
 * Output in the frontend
 * @package Msls
 */
class MslsOutput extends MslsMain {

	/**
	 * Holds the format for the output
	 * @var array $tags
	 */
	protected $tags;

	/**
	 * Creates and gets the output as an array
	 *
	 * @param int $display
	 * @param bool $filter
	 * @param bool $exists
	 *
	 * @return array
	 * @uses MslsLink
	 * @uses MslsOptions
	 */
	public function get( $display, $filter = false, $exists = false ) {
		$arr = [];

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

				if ( has_filter( 'msls_output_get' ) ) {
					/**
					 * Returns HTML-link for an item of the output-arr
					 *
					 * @param string $url
					 * @param MslsLink $link
					 * @param bool $is_current_blog
					 *
					 * @since 0.9.8
					 *
					 */
					$arr[] = ( string ) apply_filters( 'msls_output_get', $url, $link, $is_current_blog );
				} else {
					$arr[] = sprintf(
						'<a href="%s" title="%s"%s>%s</a>',
						$url,
						$link->txt,
						$is_current_blog ? ' class="current_language"' : '',
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
		$blogs    = MslsBlogCollection::instance();
		$hreflang = new HrefLang( $blogs );
		$options  = MslsOptions::create();

		$arr     = [];
		$default = '';

		foreach ( $blogs->get_objects() as $blog ) {
			$url = apply_filters( 'mlsl_output_get_alternate_links', $blog->get_url( $options ), $blog );

			if ( is_null( $url ) ) {
				continue;
			}

			$description = $blog->get_description();

			$format = '<link rel="alternate" hreflang="%s" href="%s" title="%s" />';
			if ( '' === $default ) {
				$default = sprintf( $format, 'x-default', $url, esc_attr( $description ) );
			}

			$arr[] = sprintf( $format, $hreflang->get( $blog->get_language() ), $url, esc_attr( $description ) );
		}

		if ( 1 === count( $arr ) ) {
		    return apply_filters( 'mlsl_output_get_alternate_links_default', $default );
		}

		$arr = (array) apply_filters( 'mlsl_output_get_alternate_links_arr', $arr );

		return implode( PHP_EOL,  $arr );
	}

	/**
	 * Returns a string when the object will be treated like a string
	 * @return string
	 */
	public function __toString() {
		$display = (int) $this->options->display;
		$filter  = false;
		$exists  = isset( $this->options->only_with_translation );

		$arr = $this->get( $display, $filter, $exists );
		if ( empty( $arr ) ) {
			return apply_filters( 'msls_output_no_translation_found', '' );
		}

		$tags = $this->get_tags();

		return $tags['before_output'] . $tags['before_item'] .
		       implode( $tags['after_item'] . $tags['before_item'], $arr ) .
		       $tags['after_item'] . $tags['after_output'];
	}

	/**
	 * Gets tags for the output
	 * @return array
	 */
	public function get_tags() {
		if ( empty( $this->tags ) ) {
			$this->tags = [
				'before_item'   => $this->options->before_item,
				'after_item'    => $this->options->after_item,
				'before_output' => $this->options->before_output,
				'after_output'  => $this->options->after_output,
			];

			/**
			 * Returns tags array for the output
			 *
			 * @param array $tags
			 *
			 * @since 1.0
			 *
			 */
			$this->tags = ( array ) apply_filters( 'msls_output_get_tags', $this->tags );
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
	public function set_tags( array $arr = [] ) {
		$this->tags = wp_parse_args( $this->get_tags(), $arr );

		return $this;
	}

	/**
	 * Returns true if the requirements not fulfilled
	 *
	 * @param MslsOptions|null $thing
	 * @param boolean $exists
	 * @param string $language
	 *
	 * @return boolean
	 */
	public function is_requirements_not_fulfilled( $thing, $exists, $language ) {
		if ( is_null( $thing ) ) {
			return $exists;
		}

		return MslsOptions::class != get_class( $thing ) && ! $thing->has_value( $language ) && $exists;
	}

}
