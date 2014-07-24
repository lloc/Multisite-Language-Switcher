<?php
/**
 * MslsOptions
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * General options class
 * @package Msls
 * @property bool $activate_autocomplete
 * @property int $display
 * @property int $reference_user
 * @property int $content_priority
 * @property string $description
 * @property string $before_item
 * @property string $after_item
 * @property string $before_output
 * @property string $after_output
 */
class MslsOptions extends MslsGetSet implements IMslsRegistryInstance {

	/**
	 * Args
	 * @var array
	 */
	protected $args;

	/**
	 * Name
	 * @var string
	 */
	protected $name;

	/**
	 * Exists
	 * @var bool
	 */
	protected $exists = false;

	/**
	 * Separator
	 * @var string
	 */
	protected $sep = '';

	/**
	 * Autoload
	 * @var string
	 */
	protected $autoload = 'yes';

	/**
	 * Base
	 * @var string
	 */
	protected $base;

	/**
	 * Base definition
	 * @var string
	 */
	protected $base_defined = '';

	/**
	 * Factory method
	 * @param int $id
	 * @return MslsOptions
	 */
	public static function create( $id = 0 ) {
		if ( is_admin() ) {
			$id  = (int) $id;

			if ( MslsContentTypes::create()->is_taxonomy() ) {
				return MslsOptionsTax::create( $id );
			}

			return new MslsOptionsPost( $id );
		}

		if ( self::is_main_page() ) {
			return new MslsOptions();
		}
		elseif ( self::is_tax_page() ) {
			return MslsOptionsTax::create();
		}
		elseif ( self::is_query_page() ) {
			return MslsOptionsQuery::create();
		}

		global $wp_query;

		return new MslsOptionsPost( $wp_query->get_queried_object_id() );
	}

	/**
	 * Checks if the current page is a home, front or 404 page
	 * @return boolean
	 */
	public static function is_main_page() {
		return( is_front_page() || is_search() || is_404() );
	}

	/**
	 * Checks if the current page is a category, tag or any other tax archive
	 * @return boolean
	 */
	public static function is_tax_page() {
		return( is_category() || is_tag() || is_tax() );
	}

	/**
	 * Checks if the current page is a date, author any other post_type archive
	 * @return boolean
	 */
	public static function is_query_page() {
		return( is_date() || is_author() || is_post_type_archive() );
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->args   = func_get_args();
		$this->name   = 'msls' . $this->sep . implode( $this->sep, $this->args );
		$this->exists = $this->set( get_option( $this->name ) );
		$this->base   = $this->get_base();
	}

	/**
	 * Save
	 * @param mixed $arr
	 */
	public function save( $arr ) {
		$this->delete();
		if ( $this->set( $arr ) ) {
			$arr = $this->get_arr();
			if ( ! empty( $arr ) ) {
				add_option( $this->name, $arr, '', $this->autoload );
			}
		}
	}

	/**
	 * Delete
	 */
	public function delete() {
		$this->reset();
		if ( $this->exists ) {
			delete_option( $this->name );
		}
	}

	/**
	 * Set
	 * @param mixed $arr
	 * @return bool
	 */
	public function set( $arr ) {
		if ( is_array( $arr ) ) {
			foreach ( $arr as $key => $value ) {
				$this->__set( $key, $value );
			}
			return true;
		}
		return false;
	}

	/**
	 * Get base
	 * @return string
	 */
	protected function get_base() {
		return $this->base_defined;
	}

	/**
	 * Get permalink
	 * @param string $language
	 * @return string
	 */
	public function get_permalink( $language ) {
		/**
		 * Filters the url by language
		 * @since 0.9.8
		 * @param string $postlink
		 * @param string $language
		 */
		$postlink = (string) apply_filters(
			'msls_options_get_permalink',
			$this->get_postlink( $language ),
			$language
		);
		return( '' != $postlink ? $postlink : home_url() );
	}

	/**
	 * Get postlink
	 * @param string $language
	 * @return string
	 */
	public function get_postlink( $language ) {
		return '';
	}

	/**
	 * Get current link
	 * @return string
	 */
	public function get_current_link() {
		return home_url();
	}

	/**
	 * Is excluded
	 * @return bool
	 */
	public function is_excluded() {
		return isset( $this->exclude_current_blog );
	}

	/**
	 * Is content
	 * @return bool
	 */
	public function is_content_filter() {
		return isset( $this->content_filter );
	}

	/**
	 * Get order
	 * @return string
	 */
	public function get_order() {
		return (
			isset( $this->sort_by_description ) ?
			'description' :
			'language'
		);
	}

	/**
	 * Get url
	 * @param string $dir
	 * @return string
	 */
	public function get_url( $dir ) {
		return esc_url( plugins_url( $dir, MSLS_PLUGIN__FILE__ ) );
	}

	/**
	 * Get flag url
	 * @param string $language
	 * @return string
	 */
	public function get_flag_url( $language ) {
		if ( has_filter( 'msls_options_get_flag_url' ) ) {
			/**
			 * Override the path to the flag-icons
			 * @since 0.9.9
			 * @param MslsOptions $this
			 */
			$url = (string) apply_filters( 'msls_options_get_flag_url', $this );
		}
		elseif ( ! is_admin() && isset( $this->image_url ) ) {
			$url = $this->__get( 'image_url' );
		}
		else {
			$url = $this->get_url( 'flags' );
		}
		if ( 5 == strlen( $language ) ) {
			$language = strtolower( substr( $language, -2 ) );
		}
		return sprintf( '%s/%s.png', $url, $language );
	}

	/**
	 * Get or create an instance of MslsOptions
	 * @todo Until PHP 5.2 is not longer the minimum for WordPress ...
	 * @return MslsOptions
	 */
	public static function instance() {
		if ( ! ( $obj = MslsRegistry::get_object( 'MslsOptions' ) ) ) {
			$obj = new self();
			MslsRegistry::set_object( 'MslsOptions', $obj );
		}
		return $obj;
	}

}
