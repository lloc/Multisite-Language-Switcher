<?php
/**
 * MslsOptions
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

use lloc\Msls\Component\Icon\IconPng;

/**
 * General options class
 * @package Msls
 * @property bool $activate_autocomplete
 * @property int $display
 * @property int $reference_user
 * @property int $content_priority
 * @property string $admin_language
 * @property string $description
 * @property string $before_item
 * @property string $after_item
 * @property string $before_output
 * @property string $after_output
 */
class MslsOptions extends MslsGetSet {

	/**
	 * @var string[]
	 */
	protected $args;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var bool
	 */
	protected $exists = false;

	/**
	 * @var string
	 */
	protected $sep = '';

	/**
	 * @var string
	 */
	protected $autoload = 'yes';

	/**
	 * @var string[]
	 */
	private $available_languages;

	/**
	 * @var bool
	 */
	public $with_front;

	/**
	 * Factory method
	 *
	 * @codeCoverageIgnore
	 *
	 * @param int $id
	 *
	 * @return MslsOptions
	 */
	public static function create( $id = 0 ) {
		if ( is_admin() ) {
			$id = (int) $id;

			if ( MslsContentTypes::create()->is_taxonomy() ) {
				return MslsOptionsTax::create( $id );
			}

			return new MslsOptionsPost( $id );
		}

		if ( self::is_main_page() ) {
			$options = new MslsOptions();
		} elseif ( self::is_tax_page() ) {
			$options = MslsOptionsTax::create();
		} elseif ( self::is_query_page() ) {
			$options = MslsOptionsQuery::create();
		} else {
			$options = new MslsOptionsPost( get_queried_object_id() );
		}

		add_filter( 'check_url', [ $options, 'check_for_blog_slug' ], 10, 2 );

		return $options;
	}

	/**
	 * Checks if the current page is a home, front or 404 page
	 * @return boolean
	 */
	public static function is_main_page() {
		return is_front_page() || is_search() || is_404();
	}

	/**
	 * Checks if the current page is a category, tag or any other tax archive
	 * @return boolean
	 */
	public static function is_tax_page() {
		return is_category() || is_tag() || is_tax();
	}

	/**
	 * Checks if the current page is a date, author any other post_type archive
	 * @return boolean
	 */
	public static function is_query_page() {
		return is_date() || is_author() || is_post_type_archive();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->args   = func_get_args();
		$this->name   = 'msls' . $this->sep . implode( $this->sep, $this->args );
		$this->exists = $this->set( get_option( $this->name ) );
	}

	/**
	 * Gets an element of arg by index
	 *
	 * The returning value will be cast to the type of $retval or will be the exact
	 * value of $retval if nothing is set at this index.
	 *
	 * @param int $idx
	 * @param mixed $val
	 *
	 * @return mixed
	 */
	public function get_arg( $idx, $val = null ) {
		$arg = $this->args[ $idx ] ?? $val;
		settype( $arg, gettype( $val ) );

		return $arg;
	}

	/**
	 * @param mixed $arr
	 *
	 * @codeCoverageIgnore
	 */
	public function save( $arr ): void {
		$this->delete();
		if ( $this->set( $arr ) ) {
			$arr = $this->get_arr();
			if ( ! empty( $arr ) ) {
				add_option( $this->name, $arr, '', $this->autoload );
			}
		}
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function delete(): void {
		$this->reset();
		if ( $this->exists ) {
			delete_option( $this->name );
		}
	}

	/**
	 * @param mixed $arr
	 *
	 * @return bool
	 */
	public function set( $arr ): bool {
		if ( ! is_array( $arr ) ) {
			return false;
		}

		/**
		 * Mapping for us language code
		 */
		$map = [ 'us' => 'en_US', 'en' => 'en_US' ];
		foreach ( $map as $old => $new ) {
			if ( isset( $arr[ $old ] ) ) {
				$arr[ $new ] = $arr[ $old ];
			}
		}

		foreach ( $arr as $key => $value ) {
			$this->__set( $key, $value );
		}

		return true;
	}

	/**
	 * Get permalink
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_permalink( $language ): string {
		/**
		 * Filters the url by language
		 *
		 * @param string $postlink
		 * @param string $language
		 *
		 * @since 0.9.8
		 *
		 */
		$postlink = (string) apply_filters(
			'msls_options_get_permalink',
			$this->get_postlink( $language ),
			$language
		);

		return '' != $postlink ? $postlink : home_url( '/' );
	}

	/**
	 * Get postlink
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_postlink( string $language ): string {
		return '';
	}

	/**
	 * Get the queried taxonomy
	 * @return string
	 */
	public function get_tax_query(): string {
		return '';
	}

	/**
	 * @return string
	 */
	public function get_current_link(): string {
		return home_url( '/' );
	}

	/**
	 * @return bool
	 */
	public function is_excluded(): bool {
		return isset( $this->exclude_current_blog );
	}

	/**
	 * @return bool
	 */
	public function is_content_filter(): bool {
		return isset( $this->content_filter );
	}

	/**
	 * @return string
	 */
	public function get_order(): string {
		return isset( $this->sort_by_description ) ? 'description' : 'language';
	}

	/**
	 * Get url
	 *
	 * @param string $dir
	 *
	 * @return string
	 */
	public function get_url( $dir ): string {
		return esc_url( MslsPlugin::plugins_url( $dir ) );
	}

	/**
	 * @param string $post_type
	 *
	 * @return string
	 */
	public function get_slug( $post_type ) {
		$key = "rewrite_{$post_type}";

		error_log( $key );

		return $this->$key ?? '';
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_icon( $language ): string {
		return ( new IconPng() )->get( $language );
	}

	/**
	 * Get flag url
	 *
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_flag_url( $language ) {
		if ( ! is_admin() && isset( $this->image_url ) ) {
			$url = $this->__get( 'image_url' );
		} else {
			$url = $this->get_url( 'flags' );
		}

		/**
		 * Override the path to the flag-icons
		 *
		 * @param string $url
		 *
		 * @since 0.9.9
		 *
		 */
		$url = (string) apply_filters( 'msls_options_get_flag_url', $url );

		$icon = $this->get_icon( $language );

		/**
		 * Use your own filename for the flag-icon
		 *
		 * @param string $icon
		 * @param string $language
		 *
		 * @since 1.0.3
		 *
		 */
		$icon = (string) apply_filters( 'msls_options_get_flag_icon', $icon, $language );

		return sprintf( '%s/%s', $url, $icon );
	}

	/**
	 * Get all available languages
	 *
	 * @return string[]
	 *
	 * @uses format_code_lang
	 * @uses get_available_languages
	 */
	public function get_available_languages(): array {
		if ( empty( $this->available_languages ) ) {
			$this->available_languages = [
				'en_US' => __( 'American English', 'multisite-language-switcher' ),
			];

			foreach ( get_available_languages() as $code ) {
				$this->available_languages[ esc_attr( $code ) ] = format_code_lang( $code );
			}

			/**
			 * Returns custom filtered available languages
			 *
			 * @param array $available_languages
			 *
			 * @since 1.0
			 *
			 */
			$this->available_languages = (array) apply_filters(
				'msls_options_get_available_languages',
				$this->available_languages
			);
		}

		return $this->available_languages;
	}

	/**
	 * The 'blog'-slug-problem :/
	 *
	 * @param string $url
	 * @param MslsOptions $options
	 *
	 * @return string
	 */
	public static function check_for_blog_slug( string $url, MslsOptions $options ): string {
		if ( empty( $url ) || ! is_string( $url ) ) {
			return '';
		}

		global $wp_rewrite;
		if ( ! is_subdomain_install() || ! $wp_rewrite->using_permalinks() ) {
			return $url;
		}

		$count = 1;
		$url   = str_replace( home_url(), '', $url, $count );

		global $current_site;
		$permalink_structure = get_blog_option( $current_site->blog_id, 'permalink_structure' );
		if ( $permalink_structure ) {
			list( $needle, ) = explode( '/%', $permalink_structure, 2 );

			$url = str_replace( $needle, '', $url );
			if ( is_main_site() && $options->with_front ) {
				$url = "{$needle}{$url}";
			}
		}

		return home_url( $url );
	}

}
