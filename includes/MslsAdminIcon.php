<?php

namespace lloc\Msls;

use lloc\Msls\Component\Icon\IconSvg;
use lloc\Msls\Component\Icon\IconLabel;

/**
 * Handles the icon links in the backend
 *
 * @package Msls
 */
class MslsAdminIcon {

	/**
	 * @var string
	 */
	protected $icon_type = 'action';

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var string
	 */
	public $origin_language;

	/**
	 * @var string
	 */
	protected $src;

	/**
	 * @var string
	 */
	protected $href;

	/**
	 * @var int
	 */
	protected $blog_id;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $path = 'post-new.php';

	/**
	 * The current object ID
	 *
	 * @var int
	 */
	protected $id;

	const TYPE_FLAG = 'flag';

	const TYPE_LABEL = 'label';

	/**
	 * Constructor
	 *
	 * @param string $type
	 */
	public function __construct( ?string $type ) {
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
	 * @param ?string $type
	 *
	 * @return MslsAdminIcon|MslsAdminIconTaxonomy
	 */
	public static function create( ?string $type = null ) {
		$obj = MslsContentTypes::create();

		if ( ! $type ) {
			$type = $obj->get_request();
		}

		return $obj->is_taxonomy() ? new MslsAdminIconTaxonomy( $type ) : new MslsAdminIcon( $type );
	}

	/**
	 * Set the icon path
	 *
	 * @param $icon_type
	 *
	 * @return MslsAdminIcon
	 */
	public function set_icon_type( $icon_type ): MslsAdminIcon {
		$this->icon_type = $icon_type;

		return $this;
	}

	/**
	 * Set the path by type
	 *
	 * @return MslsAdminIcon
	 */
	public function set_path(): MslsAdminIcon {
		if ( 'post' != $this->type ) {
			$query_vars = array( 'post_type' => $this->type );
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
	public function set_language( string $language ): MslsAdminIcon {
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
	public function set_src( string $src ): MslsAdminIcon {
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
	public function set_href( int $id ): MslsAdminIcon {
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
	public function set_id( int $id ): MslsAdminIcon {
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
	public function set_origin_language( string $origin_language ): MslsAdminIcon {
		$this->origin_language = $origin_language;

		return $this;
	}

	/**
	 * Get image as html-tag
	 *
	 * @return string
	 */
	public function get_img(): string {
		return sprintf( '<img alt="%s" src="%s" />', $this->language, $this->src );
	}

	/**
	 * Get link as html-tag
	 *
	 * @return string
	 */
	public function get_a(): string {
		if ( empty( $this->href ) ) {
			$title = sprintf(
				__( 'Create a new translation in the %s-blog', 'multisite-language-switcher' ),
				$this->language
			);
			$href  = $this->get_edit_new();
		} else {
			$title = sprintf(
				__( 'Edit the translation in the %s-blog', 'multisite-language-switcher' ),
				$this->language
			);
			$href  = $this->href;
		}

		return sprintf( '<a title="%s" href="%s">%s</a>&nbsp;', $title, $href, $this->get_icon() );
	}

	/**
	 * Get icon as html-tag
	 *
	 * @return string
	 */
	public function get_icon(): string {
		if ( ! $this->language ) {
			return '';
		}

		switch ( $this->icon_type ) {
			case self::TYPE_FLAG:
				$icon = sprintf(
					'<span class="flag-icon %s">%s</span>',
					( new IconSvg() )->get( $this->language ),
					$this->language
				);
				break;
			case self::TYPE_LABEL:
				$icon = sprintf(
					'<span class="language-badge %s">%s</span>',
					$this->language,
					( new IconLabel() )->get( $this->language )
				);
				break;
			default:
				$icon = sprintf(
					'<span class="dashicons %s"></span>',
					empty( $this->href ) ? 'dashicons-plus' : 'dashicons-edit'
				);
		}

		return $icon;
	}

	/**
	 * Creates new admin link
	 *
	 * @return string
	 */
	public function get_edit_new(): string {
		$path = $this->path;

		if ( null !== $this->id && null !== $this->origin_language ) {
			$path = add_query_arg(
				array(
					'msls_id'   => $this->id,
					'msls_lang' => $this->origin_language,
				),
				$this->path
			);
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
