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

	/**
	 * Holds the format for the output
	 * @var array $tags
	 */
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
	 * @param int $display
	 * @param bool $filter
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
				$url      = $mydata->get_current_link();
				$current  = ( $blog->userblog_id == MslsBlogCollection::instance()->get_current_blog_id() );

				if ( $current ) {
					$link->txt = $blog->get_description();
				}
				else {
					switch_to_blog( $blog->userblog_id );

					if ( $this->is_requirements_not_fulfilled( $mydata, $exists, $language ) ) {
						restore_current_blog();
						continue;
					}
					else {
						$url       = $mydata->get_permalink( $language );
						$link->txt = $blog->get_description();
					}

					restore_current_blog();
				}

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
					$arr[] = ( string ) apply_filters( 'msls_output_get', $url, $link, $current );
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

		$display = (int) $options->display;
		$filter  = false;
		$exists  = isset( $options->only_with_translation );

		$arr = $this->get( $display, $filter, $exists );

		if ( ! empty( $arr ) ) {
			$tags = $this->get_tags();

			return $tags['before_output'] . $tags['before_item'] .
				implode( $tags['after_item'] . $tags['before_item'], $arr ) .
				$tags['after_item'] . $tags['after_output'];

		}
		return '';
	}

	/**
	 * Gets tags for the output
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

			/**
			 * Returns tags array for the output
			 * @since 1.0
			 * @param array $tags
			 */
			$this->tags = ( array ) apply_filters( 'msls_output_get_tags', $this->tags );
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

	/**
	 * Returns true if the requirements not fulfilled
	 * @param MslsOptions|null $mydata
	 * @param boolean $exists
	 * @param string $language
	 * @return boolean
	 */
	public function is_requirements_not_fulfilled( $mydata, $exists, $language ) {
		return(
			'MslsOptions' != get_class( $mydata ) &&
			$exists &&
			( is_null( $mydata ) || ! $mydata->has_value( $language ) )
		);
	}
}
