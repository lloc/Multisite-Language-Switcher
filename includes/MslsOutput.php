<?php
/**
 * MslsOutput
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Output in the frontend
 * @package Msls
 */
class MslsOutput extends MslsMain {

	protected $tags;

	/**
	 * Init
	 * @return MslsOutput
	 */
	static function init() {
		return new self();
	}

	/**
	 * Creates and gets the output as an array
	 * @param string $display
	 * @param bool frontend
	 * @param bool $exists
	 * @uses MslsOptions
	 * @uses MslsLink
	 * @return array
	 */
	public function get( $display, $filter = false, $exists = false ) {
		$arr = array();

		$blogs = MslsBlogCollection::instance()->get_filtered( $filter );
		if ( $blogs ) {
			$mydata = MslsOptions::create();
			$link   = MslsLink::create( $display );

			foreach ( $blogs as $blog ) {
				$language = $blog->get_language();

				$current = ( $blog->userblog_id == MslsBlogCollection::instance()->get_current_blog_id() );
				if ( $current ) {
					$url = $mydata->get_current_link();
					$link->txt = $blog->get_description();
				}
				else {
					switch_to_blog( $blog->userblog_id );
					if ( 'MslsOptions' != get_class( $mydata ) && $exists && ! $mydata->has_value( $language ) ) {
						/**
						 * We set $language to false so we can first restore the current blog
						 * and continue with the next blog right after this important step.
						 */
						$language = false;
					}
					else {
						$url       = $mydata->get_permalink( $language );
						$link->txt = $blog->get_description();
					}
					restore_current_blog();
				}

				/**
				 * No language no party...
				 */
				if ( ! $language )
					continue;

				$link->src = MslsOptions::instance()->get_flag_url( $language );
				$link->alt = $language;

				if ( has_filter( 'msls_output_get' ) ) {
					/**
					 * Returns HTML-link for an item of the output-arr
					 * @since 0.9.8
					 * @param string $url
					 * @param MslsLink $link
					 * @param bool current
					 */
					$arr[] = (string) apply_filters(
						'msls_output_get',
						$url,
						$link,
						$current
					);
				}
				else {
					$arr[] = sprintf(
						'<a href="%s" title="%s"%s>%s</a>',
						$url,
						$link->txt,
						( $current ? ' class="current_language"' : '' ),
						$link
					);
				}
			}
		}

		return $arr;
	}

	/**
	 * Returns a string when the object will be treated like a string
	 * @return string
	 */
	public function __toString() {
		$options = MslsOptions::instance();
		$arr     = $this->get(
			(int) $options->display,
			false,
			isset( $options->only_with_translation )
		);

		if ( empty( $arr ) )
			return '';

		$tags = $this->get_tags();
		return $tags['before_output'] .
			$tags['before_item'] .
			implode( $tags['after_item'] . $tags['before_item'], $arr ) .
			$tags['after_item'] .
			$tags['after_output'];
	}

	/**
	 * Gets tags for the output
	 *
	 * @return array
	 */
	public function get_tags() {
		if ( empty( $this->tags ) ) {
			$options = MslsOptions::instance();

			$this->tags = array(
				'before_item'   => $options->before_item,
				'after_item'    => $options->after_item,
				'before_output' => $options->before_output,
				'after_output'  => $options->after_output,
			);
		}
		return $this->tags;
	}

	/**
	 * Sets tags for the output
	 *
	 * @param array $arr
	 * @return MslsOutput
	 */
	public function set_tags( array $arr = array() ) {
		$this->tags = wp_parse_args( $this->get_tags(), $arr );
		return $this;
	}

}
