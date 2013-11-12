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
	public static function init() {
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

		$blogs = $this->blogs->get_filtered( $filter );
		if ( $blogs ) {
			$mydata = MslsOptions::create();
			$link   = MslsLink::create( $display );

			foreach ( $blogs as $blog ) {
				$language = $blog->get_language();

				$current = ( $blog->userblog_id == $this->blogs->get_current_blog_id() );
				if ( $current ) {
					$url = $mydata->get_current_link();
				}
				else {
					switch_to_blog( $blog->userblog_id );
					if ( 'MslsOptions' != get_class( $mydata ) && $exists && ! $mydata->has_value( $language ) ) {
						restore_current_blog();
						continue;
					}
					$url = $mydata->get_permalink( $language );
					restore_current_blog();
				}
				$link->txt = $blog->get_description();
				$link->src = $this->options->get_flag_url( $language );
				$link->alt = $language;
				if ( has_filter( 'msls_output_get' ) ) {
					$arr[] = apply_filters(
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
	 * @see get_the_msls()
	 * @return string
	 */
	public function __toString() {
		$arr = $this->get(
			(int) $this->options->display,
			false,
			isset( $this->options->only_with_translation )
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
	 * @return array
	 */
	public function get_tags() {
		if ( empty( $this->tags ) ) {
			$this->tags = array(
				'before_item'   => $this->options->before_item,
				'after_item'    => $this->options->after_item,
				'before_output' => $this->options->before_output,
				'after_output'  => $this->options->after_output,
			);
		}
		return $this->tags;
	}

	/**
	 * Sets tags for the output
	 * @param array $arr
	 * @return MslsOutput
	 */
	public function set_tags( array $arr = array() ) {
		$this->tags = wp_parse_args( $this->get_tags(), $arr );
		return $this;
	}

}
