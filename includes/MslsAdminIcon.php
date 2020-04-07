<?php
/**
 * MslsAdminIcon
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

use lloc\Msls\Component\Icon\IconSvg;

/**
 * Handles the icon links in the backend
 * @package Msls
 */
class MslsAdminIcon {
	/**
	 * IconType
	 * @var string
	 */
	protected $iconType = 'action';

	/**
	 * Language
	 * @var string
	 */
	protected $language;

	/**
	 * Origin Language
	 * @var string
	 */
	public $origin_language;

	/**
	 * Source
	 * @var string
	 */
	protected $src;

	/**
	 * URL
	 * @var string
	 */
	protected $href;

	/**
	 * Blog id
	 * @var int
	 */
	protected $blog_id;

	/**
	 * Type
	 * @var string
	 */
	protected $type;

	/**
	 * Path
	 * @var string
	 */
	protected $path = 'post-new.php';

	/**
	 * The current object ID
	 * @var int
	 */
	protected $id;

	/**
	 * Constructor
	 *
	 * @param string $type
	 */
	public function __construct( $type ) {
		$this->type = esc_attr( $type );
		$this->set_path();
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->get_a();
	}

	/**
	 * @codeCoverageIgnore
	 *
	 * @return MslsAdminIcon
	 */
	public static function create() {
		$obj = MslsContentTypes::create();

		$type = $obj->get_request();
		if ( $obj->is_taxonomy() ) {
			return new MslsAdminIconTaxonomy( $type );
		}

		return new MslsAdminIcon( $type );
	}

	/**
	 * Set the icon path
	 *
	 * @return MslsAdminIcon
	 */
	public function set_icon_type( $iconType ) {
		$this->iconType = $iconType;

		return $this;
	}

	/**
	 * Set the path by type
	 *
	 * @return MslsAdminIcon
	 */
	public function set_path() {
		if ( 'post' != $this->type ) {
			$query_vars = [ 'post_type' => $this->type ];
			$this->path = add_query_arg( $query_vars, $this->path );
		}

		return $this;
	}

	/**
	 * Set language
	 *
	 * @param string $language
	 *
	 * @return MslsAdminIcon
	 */
	public function set_language( $language ) {
		$this->language = $language;

		return $this;
	}

	/**
	 * Set src
	 *
	 * @param string $src
	 *
	 * @return MslsAdminIcon
	 */
	public function set_src( $src ) {
		$this->src = $src;

		return $this;
	}

	/**
	 * Set href
	 *
	 * @param int $id
	 *
	 * @return MslsAdminIcon
	 */
	public function set_href( $id ) {
		$this->href = get_edit_post_link( $id );

		return $this;
	}

	/**
	 * Sets the id of the object this icon is for
	 *
	 * @param int $id
	 *
	 * @return MslsAdminIcon
	 */
	public function set_id( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * Sets the origin language for this icon
	 *
	 * @param string $origin_language
	 *
	 * @return MslsAdminIcon
	 */
	public function set_origin_language( $origin_language ) {
		$this->origin_language = $origin_language;

		return $this;
	}

	/**
	 * Get image as html-tag
	 *
	 * @return string
	 */
	public function get_img() {
		return sprintf( '<img alt="%s" src="%s" />', $this->language, $this->src );
	}

	/**
	 * Get link as html-tag
	 *
	 * @return string
	 */
	public function get_a(): string {
		if ( empty( $this->href ) ) {
			$title = sprintf( __( 'Create a new translation in the %s-blog', 'multisite-language-switcher' ), $this->language );
			$href = $this->get_edit_new();
		} else {
			$title = sprintf( __( 'Edit the translation in the %s-blog', 'multisite-language-switcher' ), $this->language );
			$href  = $this->href;
		}

		return sprintf( '<a title="%s" href="%s">%s</a>&nbsp;', $title, $href, $this->get_icon() );
	}

	/**
	 * Get icon as html-tag
	 *
	 * @return string
	 */
	public function get_icon() {
		if ( 'flag' === $this->iconType ) {
			return sprintf( '<span class="flag-icon %s">%s</span>',
				( new IconSvg() )->get( $this->language ),
				$this->language
			);
		}

		if ( empty( $this->href ) ) {
			return '<span class="dashicons dashicons-plus"></span>';
		}

		return '<span class="dashicons dashicons-edit"></span>';
	}

	/**
	 * Creates new admin link
	 *
	 * @return string
	 */
	public function get_edit_new() {
		$path = $this->path;

		if ( null !== $this->id && null !== $this->origin_language ) {
			$path = add_query_arg( [ 'msls_id' => $this->id, 'msls_lang' => $this->origin_language ], $this->path );
		}

		/**
		 * Returns custom url of an admin icon link
		 *
		 * @param string $path
		 *
		 * @since 0.9.9
		 */
		$path = (string) apply_filters( 'msls_admin_icon_get_edit_new', $path );

		return get_admin_url( get_current_blog_id(), $path );
	}

}
