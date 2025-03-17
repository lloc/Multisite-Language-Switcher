<?php declare( strict_types=1 );

namespace lloc\Msls;

use lloc\Msls\Component\Icon\IconPng;

/**
 * General options class
 *
 * @package Msls
 * @property bool $activate_autocomplete
 * @property bool $activate_content_import
 * @property bool $output_current_blog
 * @property bool $only_with_translation
 * @property int $content_priority
 * @property int $display
 * @property int $reference_user
 * @property string $admin_display
 * @property string $admin_language
 * @property string $after_item
 * @property string $after_output
 * @property string $before_item
 * @property string $before_output
 * @property string $content_filter
 * @property string $description
 * @property string $exclude_current_blog
 * @property string $sort_by_description
 */
class MslsOptions extends MslsGetSet implements OptionsInterface {

	public const PREFIX    = 'msls';
	public const SEPARATOR = '';

	protected string $name;
	protected bool $exists   = false;
	protected bool $autoload = true;

	/**
	 * @var array<int, mixed>
	 */
	protected array $args;

	/**
	 * @var array<string, string>
	 */
	private array $available_languages;

	/**
	 * Rewrite with front
	 *
	 * @var bool
	 */
	public ?bool $with_front = null;

	/**
	 * Factory method
	 *
	 * @codeCoverageIgnore
	 *
	 * @param int $id
	 *
	 * @return OptionsInterface
	 */
	public static function create( $id = 0 ) {
		if ( is_admin() ) {
			$id = (int) $id;

			if ( msls_content_types()->is_taxonomy() ) {
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

		add_filter( 'msls_get_postlink', array( $options, 'check_for_blog_slug' ), 10, 2 );

		return $options;
	}

	/**
	 * Determines if the current page is the main page (front page, search, 404).
	 *
	 * @return boolean
	 */
	public static function is_main_page() {
		return is_front_page() || is_search() || is_404();
	}

	/**
	 * Determines if the current page is a category, tag or taxonomy page.
	 *
	 * @return boolean
	 */
	public static function is_tax_page() {
		return is_category() || is_tag() || is_tax();
	}

	/**
	 * Determines if the current page is an archive page for a date, author, or any other post type.
	 *
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
		$this->name   = $this->get_option_name();
		$this->exists = $this->set( get_option( $this->name ) );
	}

	public function get_option_name(): string {
		return self::PREFIX . static::SEPARATOR . implode( static::SEPARATOR, $this->args );
	}

	/**
	 * Gets an element of arg by index
	 *
	 * The returned value will either be cast to the type of `$default` or, if nothing is set at this index, it will be the value of `$default`.
	 *
	 * @param int   $index
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get_arg( int $index, $default = null ) {
		$arg = $this->args[ $index ] ?? $default;

		settype( $arg, gettype( $default ) );

		return $arg;
	}

	/**
	 * @param mixed $arr
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
		$map = array(
			'us' => 'en_US',
			'en' => 'en_US',
		);
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

	public function get_permalink( string $language ): string {
		/**
		 * Filters the url by language
		 *
		 * @param string $postlink
		 * @param string $language
		 *
		 * @since 0.9.8
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
	public function get_postlink( $language ) {
		return '';
	}

	/**
	 * Get the queried taxonomy
	 *
	 * @return string
	 */
	public function get_tax_query() {
		return '';
	}

	/**
	 * Get current link
	 */
	public function get_current_link(): string {
		return home_url( '/' );
	}

	/**
	 * Is excluded
	 *
	 * @return bool
	 */
	public function is_excluded() {
		return isset( $this->exclude_current_blog );
	}

	/**
	 * Is content
	 *
	 * @return bool
	 */
	public function is_content_filter(): bool {
		return isset( $this->content_filter );
	}

	/**
	 * Get order
	 *
	 * @return string
	 */
	public function get_order() {
		return isset( $this->sort_by_description ) ? 'description' : 'language';
	}

	/**
	 * Get url
	 *
	 * @param string $dir
	 *
	 * @return string
	 */
	public function get_url( $dir ) {
		return esc_url( MslsPlugin::plugins_url( $dir ) );
	}

	/**
	 * Returns slug for a post type
	 *
	 * @todo This method is not used anywhere in the codebase. Should it be removed?
	 *
	 * @param string $post_type
	 *
	 * @return string
	 */
	public function get_slug( string $post_type ): string {
		$key = "rewrite_{$post_type}";

		return $this->$key ?? '';
	}

	/**
	 * @param string $language
	 *
	 * @return string
	 */
	public function get_icon( $language ) {
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
		$url = ! is_admin() && isset( $this->image_url ) ? $this->__get( 'image_url' ) : $this->get_url( 'flags' );

		/**
		 * Override the path to the flag-icons
		 *
		 * @param string $url
		 *
		 * @since 0.9.9
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
		 */
		$icon = (string) apply_filters( 'msls_options_get_flag_icon', $icon, $language );

		return sprintf( '%s/%s', $url, $icon );
	}

	/**
	 * Get all available languages
	 *
	 * @return array<string, string>
	 */
	public function get_available_languages(): array {
		if ( empty( $this->available_languages ) ) {
			$this->available_languages = array(
				'en_US' => __( 'American English', 'multisite-language-switcher' ),
			);

			foreach ( get_available_languages() as $code ) {
				$this->available_languages[ esc_attr( $code ) ] = format_code_lang( $code );
			}

			/**
			 * Returns custom filtered available languages
			 *
			 * @param array $available_languages
			 *
			 * @since 1.0
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
	 * @param mixed       $url
	 * @param MslsOptions $options
	 *
	 * @return string
	 */
	public static function check_for_blog_slug( $url, $options ) {
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

	/**
	 * Get the icon type by the settings saved in admin_display
	 *
	 * @return string
	 */
	public function get_icon_type(): string {
		return MslsAdminIcon::TYPE_LABEL === $this->admin_display ? MslsAdminIcon::TYPE_LABEL : MslsAdminIcon::TYPE_FLAG;
	}
}
