<?php
/**
 * MslsAdminIcon
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Handles the icon links in the backend
 * @package Msls
 */
class MslsAdminIcon {

	/**
	 * Language
	 * @var string
	 */
	protected $language;

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
	 * Factory method
	 * @return MslsAdminIcon
	 */
	public static function create() {
		$obj  = MslsContentTypes::create();
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
	 * Set the path by type
	 * @uses add_query_arg()
	 * @return MslsAdminIcon
	 */
	public function set_path() {
		if ( 'post' != $this->type ) {
			$this->path = add_query_arg(
				array( 'post_type' => $this->type ),
				$this->path
			);
		}
		return $this;
	}

	/**
	 * Set language
	 * @param string $language
	 * @return MslsAdminIcon
	 */
	public function set_language( $language ) {
		$this->language = $language;
		return $this;
	}

	/**
	 * Set src
	 * @param string $src
	 * @return MslsAdminIcon
	 */
	public function set_src( $src ) {
		$this->src = $src;
		return $this;
	}

	/**
	 * Set href
	 * @uses get_edit_post_link()
	 * @param int $id
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
	 * @return string
	 */
	public function get_img() {
		return sprintf(
			'<img alt="%s" src="%s" />',
			$this->language,
			$this->src
		);
	}

	/**
	 * Get link as html-tag
	 * @return string
	 */
	public function get_a() {
		if ( ! empty( $this->href ) ) {
			$href  = $this->href;
			$title = sprintf(
				__( 'Edit the translation in the %s-blog', 'multisite-language-switcher' ),
				$this->language
			);
		}
		else {
			$href  = $this->get_edit_new();
			$title = sprintf(
				__( 'Create a new translation in the %s-blog', 'multisite-language-switcher' ),
				$this->language
			);
		}
		return sprintf(
			'<a title="%s" href="%s">%s</a>&nbsp;',
			$title,
			$href,
			$this->get_img()
		);
	}

	/**
	 * Creates new admin link
	 * @uses get_admin_url()
	 * @todo check if we need this method separately
	 * @return string
	 */
	public function get_edit_new() {
		/**
		 * Returns custom url of an admin icon link
		 * @since 0.9.9
		 * @param string $path
		 */
		return get_admin_url(
			get_current_blog_id(),
			(string) apply_filters( 'msls_admin_icon_get_edit_new', $this->path )
		);
	}

}
