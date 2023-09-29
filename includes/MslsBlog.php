<?php

namespace lloc\Msls;

/**
 * Internal representation of a blog
 *
 * @property int $userblog_id
 * @package Msls
 */
class MslsBlog {

	/**
	 * WordPress generates such an object
	 * @var \StdClass
	 */
	private $obj;

	/**
	 * Language-code eg. de_DE
	 * @var string
	 */
	private $language;

	/**
	 * Description eg. Deutsch
	 * @var string
	 */
	private $description;

	/**
	 * Constructor
	 *
	 * @param \StdClass $obj
	 * @param string $description
	 */
	public function __construct( $obj, $description ) {
		if ( is_object( $obj ) ) {
			$this->obj      = $obj;
			$this->language = MslsBlogCollection::get_blog_language( $this->obj->userblog_id );
		}

		$this->description = (string) $description;
	}

	/**
	 * Gets a member of the \StdClass-object by name
	 *
	 * The method return <em>null</em> if the requested member does not exists.
	 *
	 * @param string $key
	 *
	 * @return mixed|null
	 */
	final public function __get( $key ) {
		return $this->obj->$key ?? null;
	}

	/**
	 * Gets the description stored in this object
	 *
	 * The method returns the stored language if the description is empty.
	 * @return string
	 */
	public function get_description(): string {
		return empty( $this->description ) ? $this->get_language() : $this->description;
	}

	/**
	 * Gets a customized title for the blog
	 *
	 * @return string
	 */
	public function get_title(): string {
		return sprintf( '%1$s (%2$s)', $this->obj->blogname, $this->get_description() );
	}

	/**
	 * Gets the language stored in this object
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	public function get_language( $default = 'en_US' ) {
		return empty( $this->language ) ? $default : $this->language;
	}

	/**
	 * Gets the alpha2-part of the language-code
	 *
	 * @return string
	 */
	public function get_alpha2() {
		$language = $this->get_language();

		return substr( $language, 0, 2 );
	}

	/**
	 * @param MslsOptions $options
	 *
	 * @return string|null
	 */
	public function get_url( $options ) {
		if ( $this->obj->userblog_id == MslsBlogCollection::instance()->get_current_blog_id() ) {
			return $options->get_current_link();
		}

		return $this->get_permalink( $options );
	}

	/**
	 * @param MslsOptions $options
	 *
	 * @return string|null
	 */
	protected function get_permalink( $options ) {
		$url = null;

		$is_home = is_front_page();

		switch_to_blog( $this->obj->userblog_id );

		if ( is_object( $options ) && method_exists( $options, 'has_value' ) && ( $is_home || $options->has_value( $this->get_language() ) ) ) {
			$url = apply_filters( 'mlsl_blog_get_permalink', $options->get_permalink( $this->get_language() ), $this );
		}

		restore_current_blog();

		return $url;
	}

	/**
	 * Sort objects helper
	 *
	 * @param string $a
	 * @param string $b
	 *
	 * @return int
	 */
	public static function _cmp( $a, $b ) {
		if ( $a == $b ) {
			return 0;
		}

		return ( $a < $b ? ( - 1 ) : 1 );
	}

	/**
	 * Sort objects by language
	 *
	 * @param MslsBlog $a
	 * @param MslsBlog $b
	 *
	 * @return int
	 */
	public static function language( MslsBlog $a, MslsBlog $b ) {
		return self::_cmp( $a->get_language(), $b->get_language() );
	}

	/**
	 * Sort objects by description
	 *
	 * @param MslsBlog $a
	 * @param MslsBlog $b
	 *
	 * @return int
	 */
	public static function description( MslsBlog $a, MslsBlog $b ) {
		return self::_cmp( $a->get_description(), $b->get_description() );
	}

	/**
	 * @return string
	 */
	public function get_blavatar(): string {
		$blavatar_html   = '<div class="blavatar"></div>';
		$show_site_icons = apply_filters( 'wp_admin_bar_show_site_icons', true );

		switch_to_blog( $this->obj->userblog_id );

		if ( true === $show_site_icons && has_site_icon( $this->obj->userblog_id ) ) {
			$blavatar_html = sprintf(
				'<img class="blavatar" src="%s" srcset="%s 2x" alt="" width="16" height="16"%s />',
				esc_url( get_site_icon_url( 16 ) ),
				esc_url( get_site_icon_url( 32 ) ),
				( wp_lazy_loading_enabled( 'img', 'site_icon_in_toolbar' ) ? ' loading="lazy"' : '' )
			);
		}

		restore_current_blog();

		return $blavatar_html;
	}

}
