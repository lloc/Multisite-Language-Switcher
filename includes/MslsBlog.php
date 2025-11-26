<?php declare( strict_types=1 );

namespace lloc\Msls;

/**
 * Internal representation of a blog
 *
 * @property int $userblog_id
 * @package Msls
 */
class MslsBlog {

	const MSLS_GET_PERMALINK_HOOK = 'msls_blog_get_permalink';

	const WP_ADMIN_BAR_SHOW_SITE_ICONS_HOOK = 'wp_admin_bar_show_site_icons';

	/**
	 * WordPress generates such an object
	 *
	 * @var \StdClass
	 */
	private $obj;

	/**
	 * Language-code e.g. "de_DE", or "en_US", or "it_IT"
	 *
	 * @var string
	 */
	private string $language;

	/**
	 * Description e.g. "Deutsch", or "English", or "Italiano"
	 *
	 * @var string
	 */
	private string $description;

	/**
	 * Constructor
	 *
	 * @param ?\StdClass $obj
	 * @param string     $description
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
	 *
	 * @return string
	 */
	public function get_description(): string {
		return empty( $this->description ) ? $this->get_language() : $this->description;
	}

	/**
	 * Gets a customized title for the blog
	 *
	 * @param string $icon_type
	 *
	 * @return string
	 */
	public function get_title( string $icon_type = 'flag' ): string {
		$icon = ( new MslsAdminIcon( null ) )->set_language( $this->language )->set_icon_type( $icon_type );

		return sprintf(
			'%1$s %2$s',
			$this->obj->blogname,
			'<span class="msls-icon-wrapper flag">' . $icon->get_icon() . '</span>'
		);
	}

	/**
	 * Gets the language stored in this object
	 *
	 * @param string $preset
	 *
	 * @return string
	 */
	public function get_language( $preset = 'en_US' ) {
		return empty( $this->language ) ? $preset : $this->language;
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
	 * @param OptionsInterface $options
	 *
	 * @return string|null
	 */
	public function get_url( $options ) {
		if ( msls_blog_collection()->get_current_blog_id() === $this->obj->userblog_id ) {
			return $options->get_current_link();
		}

		return $this->get_permalink( $options );
	}

	/**
	 * @param OptionsInterface $options
	 *
	 * @return ?string
	 */
	protected function get_permalink( OptionsInterface $options ) {
		$url = null;

		$is_home = is_front_page();

		switch_to_blog( $this->obj->userblog_id );

		if ( $is_home || $options->has_value( $this->get_language() ) ) {
			$url = apply_filters( self::MSLS_GET_PERMALINK_HOOK, $options->get_permalink( $this->get_language() ), $this );
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
	public static function internal_cmp( $a, $b ) {
		if ( $a === $b ) {
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
		return self::internal_cmp( $a->get_language(), $b->get_language() );
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
		return self::internal_cmp( $a->get_description(), $b->get_description() );
	}

	/**
	 * @return string
	 */
	public function get_blavatar(): string {
		$blavatar_html   = '<div class="blavatar"></div>';
		$show_site_icons = apply_filters( self::WP_ADMIN_BAR_SHOW_SITE_ICONS_HOOK, true );

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
