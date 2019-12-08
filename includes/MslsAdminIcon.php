<?php
/**
 * MslsAdminIcon
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

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
	 * Factory method
	 *
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
	 * Constructor
	 * @param string $type
	 */
	public function __construct( $type ) {
		$this->type = esc_attr( $type );
		$this->set_path();
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
	 * Handles the output when object is treated like a string
	 * @return string
	 */
	public function __toString() {
		return $this->get_a();
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
	/**
	 * Get link as html-tag
	 *
	 * @return string
	 */
	public function get_a() {
		if ( ! empty( $this->href ) ) {
			$str = __( 'Edit the translation in the %s-blog', 'multisite-language-switcher' );			
			$href = $this->href;
		}
		else {
			$str = __( 'Create a new translation in the %s-blog', 'multisite-language-switcher' );			
			$href = $this->get_edit_new();
		}

		$title = sprintf( $str, $this->language );

		return sprintf( '<a title="%s" href="%s">%s</a>&nbsp;', $title, $href, $this->get_icon() );
	}

	/**
	 * Get icon as html-tag
	 *
	 * @return string
	 */
	public function get_icon() {
		if( $this->iconType === 'flag' ) {
			$icon = sprintf( 
				'<span class="flag-icon flag-icon-%s flag-icon">%s</span>',
				substr( $this->language, 0, 2 ),
				// locale_get_display_language( substr( $this->language, 0, 2 ), substr( get_option( 'WPLANG' ), 0, 2 ) ),
				\Locale::getDisplayLanguage( substr( $this->language, 0, 2 ), get_user_locale() ) 
			);
		} else {
			if ( ! empty( $this->href ) ) {
				$icon = '<span class="dashicons dashicons-edit"></span>';				
			} else {
				$icon = '<span class="dashicons dashicons-plus"></span>';							
			}	
		}

		return $icon;
	}

	/**
	 * Creates new admin link
	 *
	 * @todo check if we need this method separately
	 * @return string
	 */
	public function get_edit_new() {
		$path = $this->path;

		if ( null !== $this->id && null !== $this->origin_language ) {
			$path = add_query_arg(
				array( 'msls_id' => $this->id, 'msls_lang' => $this->origin_language ),
				$this->path
			);
		}

		/**
		 * Returns custom url of an admin icon link
		 * @since 0.9.9
		 * @param string $path
		 */
		return get_admin_url(
			get_current_blog_id(),
			(string) apply_filters( 'msls_admin_icon_get_edit_new', $path )
		);
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

}
