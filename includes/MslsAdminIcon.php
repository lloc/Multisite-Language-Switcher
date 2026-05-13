<?php declare( strict_types=1 );

namespace lloc\Msls;

use lloc\Msls\Component\Component;
use lloc\Msls\Component\Icon\IconSvg;
use lloc\Msls\Component\Icon\IconLabel;

/**
 * Handles the icon links in the backend
 *
 * @package Msls
 */
class MslsAdminIcon {

	protected string $icon_type = 'action';
	protected string $language;
	public string $origin_language;
	protected string $src;
	protected string $href;
	protected int $blog_id;
	protected string $type;
	protected string $path = 'post-new.php';
	protected int $id;

	const TYPE_FLAG  = 'flag';
	const TYPE_LABEL = 'label';

	/**
	 * Constructor
	 *
	 * @param string $type
	 */
	public function __construct( ?string $type = null ) {
		$this->type = $type ?? '';

		$this->set_path();
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->get_a();
	}

	/**
	 * @param ?string $type
	 *
	 * @return MslsAdminIcon|MslsAdminIconTaxonomy
	 */
	public static function create( ?string $type = null ) {
		$obj = msls_content_types();

		if ( ! $type ) {
			$type = $obj->get_request();
		}

		return $obj->is_taxonomy() ? new MslsAdminIconTaxonomy( $type ) : new MslsAdminIcon( $type );
	}

	/**
	 * Set the icon path
	 *
	 * @param string $icon_type
	 *
	 * @return MslsAdminIcon
	 */
	public function set_icon_type( string $icon_type ): MslsAdminIcon {
		$this->icon_type = $icon_type;

		return $this;
	}

	/**
	 * Set the path by type
	 *
	 * @return MslsAdminIcon
	 */
	public function set_path(): MslsAdminIcon {
		if ( 'post' !== $this->type ) {
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
		$this->href = get_edit_post_link( $id ) ?? '';

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
			if ( $this->should_quick_create() ) {
				return $this->get_quick_create_a();
			}

			/* translators: %s: blog name */
			$format = __( 'Create a new translation in the %s-blog', 'multisite-language-switcher' );
			$href   = $this->get_edit_new();
		} else {
			/* translators: %s: blog name */
			$format = __( 'Edit the translation in the %s-blog', 'multisite-language-switcher' );
			$href   = $this->href;
		}

		$title = sprintf( $format, $this->language );

		return sprintf( '<a title="%1$s" href="%2$s">%3$s</a>&nbsp;', esc_attr( $title ), esc_url( $href ), $this->get_icon() );
	}

	/**
	 * @return bool
	 */
	protected function should_quick_create(): bool {
		return 0 !== $this->id
			&& '' !== $this->origin_language
			&& msls_options()->activate_quick_create;
	}

	/**
	 * @return string
	 */
	protected function get_quick_create_a(): string {
		$collection     = msls_blog_collection();
		$source_blog_id = $collection->get_blog_id( $this->origin_language );
		$target_blog_id = get_current_blog_id();

		/* translators: %s: blog name */
		$format = __( 'Create a new translation in the %s-blog', 'multisite-language-switcher' );
		$title  = sprintf( $format, $this->language );

		return sprintf(
			'<button type="button" class="msls-quick-create" title="%1$s" aria-label="%1$s" data-target-blog-id="%2$d" data-source-post-id="%3$d" data-source-blog-id="%4$d">%5$s</button>&nbsp;',
			esc_attr( $title ),
			$target_blog_id,
			$this->id,
			$source_blog_id,
			$this->get_icon()
		);
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
				$class = ( new IconSvg() )->get( $this->language );
				$icon  = sprintf(
					'<span class="flag-icon %s">%s</span>',
					esc_attr( $class ),
					esc_html( $this->language )
				);
				break;
			case self::TYPE_LABEL:
				$text = ( new IconLabel() )->get( $this->language );
				$icon = sprintf(
					'<span class="language-badge %s">%s</span>',
					esc_attr( $this->language ),
					$text
				);

				break;
			default:
				$class = empty( $this->href ) ? 'dashicons-plus' : 'dashicons-edit';
				$icon  = sprintf( '<span class="dashicons %s"></span>', esc_attr( $class ) );
		}

		return wp_kses( $icon, Component::get_allowed_html() );
	}

	/**
	 * Creates new admin link
	 *
	 * @return string
	 */
	public function get_edit_new(): string {
		$path = $this->path;

		if ( 0 !== $this->id && '' !== $this->origin_language ) {
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
