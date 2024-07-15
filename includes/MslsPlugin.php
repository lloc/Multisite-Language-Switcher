<?php

namespace lloc\Msls;

use lloc\Msls\Query\BlogsInNetworkQuery;
use lloc\Msls\Query\CleanupOptionsQuery;

/**
 * Provides functionalities for general hooks and activation/deactivation
 *
 * @package Msls
 */
class MslsPlugin {

	/**
	 * Injected MslsOptions object
	 *
	 * @var MslsOptions
	 */
	protected $options;

	/**
	 * MslsPlugin constructor.
	 *
	 * @param MslsOptions $options
	 */
	public function __construct( MslsOptions $options ) {
		$this->options = $options;
	}

	/**
	 * Factory
	 *
	 * @codeCoverageIgnore
	 *
	 * @return MslsPlugin
	 */
	public static function init() {
		$obj = new self( msls_options() );

		add_action( 'plugins_loaded', array( $obj, 'init_i18n_support' ) );

		register_activation_hook( self::file(), array( $obj, 'activate' ) );

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			add_action( 'admin_enqueue_scripts', array( $obj, 'custom_enqueue' ) );
			add_action( 'wp_enqueue_scripts', array( $obj, 'custom_enqueue' ) );

			add_action( 'init', array( MslsAdminBar::class, 'init' ) );
			add_action( 'init', array( MslsBlock::class, 'init' ) );
			add_action( 'init', array( MslsShortCode::class, 'init' ) );
			add_action( 'init', array( MslsContentFilter::class, 'init' ) );
			add_action( 'widgets_init', array( MslsWidget::class, 'init' ) );
			add_action( 'wp_head', array( __CLASS__, 'print_alternate_links' ) );

			add_filter( 'msls_get_output', array( __CLASS__, 'get_output' ) );

			\lloc\Msls\ContentImport\Service::instance()->register();

			if ( is_admin() ) {
				add_action( 'admin_menu', array( MslsAdmin::class, 'init' ) );
				add_action( 'load-post.php', array( MslsMetaBox::class, 'init' ) );
				add_action( 'load-post-new.php', array( MslsMetaBox::class, 'init' ) );
				add_action( 'load-edit.php', array( MslsCustomColumn::class, 'init' ) );
				add_action( 'load-edit.php', array( MslsCustomFilter::class, 'init' ) );

				add_action( 'load-edit-tags.php', array( MslsCustomColumnTaxonomy::class, 'init' ) );
				add_action( 'load-edit-tags.php', array( MslsPostTag::class, 'init' ) );
				add_action( 'load-term.php', array( MslsPostTag::class, 'init' ) );

				if ( MslsRequest::has_var( MslsFields::FIELD_ACTION ) ) {
					$action = MslsRequest::has_var( MslsFields::FIELD_ACTION );

					if ( 'add-tag' === $action ) {
						add_action( 'admin_init', array( MslsPostTag::class, 'init' ) );
					} elseif ( 'inline-save' === $action ) {
						add_action( 'admin_init', array( MslsCustomColumn::class, 'init' ) );
					} elseif ( 'inline-save-tax' === $action ) {
						add_action( 'admin_init', array( MslsCustomColumnTaxonomy::class, 'init' ) );
					}
				}

				add_action( 'wp_ajax_suggest_posts', array( MslsMetaBox::class, 'suggest' ) );
				add_action( 'wp_ajax_suggest_terms', array( MslsPostTag::class, 'suggest' ) );
			}
		} else {
			add_action(
				'admin_notices',
				function () {
					/* translators: %s: URL to the WordPress Codex. */
					$format  = __(
						'The Multisite Language Switcher needs the activation of the multisite-feature for working properly. Please read <a onclick="window.open(this.href); return false;" href="%s">this post</a> if you don\'t know the meaning.',
						'multisite-language-switcher'
					);
					$message = sprintf(
						$format,
						esc_url( 'https://developer.wordpress.org/advanced-administration/multisite/create-network/' )
					);

					self::message_handler( $message );
				}
			);
		}

		return $obj;
	}

	/**
	 * Gets MslsOutput object
	 *
	 * @return MslsOutput
	 */
	public static function get_output() {
		static $obj = null;

		if ( is_null( $obj ) ) {
			$obj = MslsOutput::init();
		}

		return $obj;
	}

	/**
	 * Callback for action wp_head
	 */
	public static function print_alternate_links() {
		echo self::get_output()->get_alternate_links(), PHP_EOL;
	}

	/**
	 * Loads styles and some js if needed
	 *
	 * The method returns true if the autocomplete-option is activated, false otherwise.
	 *
	 * @return boolean
	 */
	public function custom_enqueue() {
		if ( ! is_admin_bar_showing() ) {
			return false;
		}

		$ver    = defined( 'MSLS_PLUGIN_VERSION' ) ? constant( 'MSLS_PLUGIN_VERSION' ) : false;
		$folder = defined( 'SCRIPT_DEBUG' ) && constant( 'SCRIPT_DEBUG' ) ? 'src' : 'js';

		wp_enqueue_style( 'msls-styles', self::plugins_url( 'css/msls.css' ), array(), $ver );
		wp_enqueue_style( 'msls-flags', self::plugins_url( 'css-flags/css/flag-icon.min.css' ), array(), $ver );

		if ( $this->options->activate_autocomplete ) {
			wp_enqueue_script( 'msls-autocomplete', self::plugins_url( "$folder/msls.js" ), array( 'jquery-ui-autocomplete' ), $ver, array( 'in_footer' => true ) );

			return true;
		}

		return false;
	}

	/**
	 * Wrapper for plugins_url
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function plugins_url( string $path ): string {
		return plugins_url( $path, self::file() );
	}

	/**
	 * Wrapper for plugin_dir_path
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function plugin_dir_path( string $path ): string {
		return plugin_dir_path( self::file() ) . $path;
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	public static function dirname( string $path ): string {
		return dirname( self::path() ) . $path;
	}

	/**
	 * @return string
	 */
	public static function file(): string {
		return defined( 'MSLS_PLUGIN__FILE__' ) ? constant( 'MSLS_PLUGIN__FILE__' ) : '';
	}

	/**
	 * @return string
	 */
	public static function path(): string {
		return defined( 'MSLS_PLUGIN_PATH' ) ? constant( 'MSLS_PLUGIN_PATH' ) : '';
	}

	/**
	 * Load textdomain
	 *
	 * The method should be executed always on init because we have some
	 * translatable string in the frontend too.
	 *
	 * @return boolean
	 */
	public function init_i18n_support() {
		return load_plugin_textdomain( 'multisite-language-switcher', false, self::dirname( '/languages/' ) );
	}

	/**
	 * Message handler
	 *
	 * Prints a message box to the screen.
	 *
	 * @param string $message
	 * @param string $css_class
	 *
	 * @return boolean
	 */
	public static function message_handler( $message, $css_class = 'error' ) {
		if ( ! empty( $message ) ) {
			printf( '<div id="msls-warning" class="%s"><p>%s</p></div>', $css_class, $message );

			return true;
		}

		return false;
	}

	/**
	 * Activate plugin
	 */
	public static function activate() {
		register_uninstall_hook( self::file(), array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Uninstall plugin
	 *
	 * The plugin data in all blogs of the current network will be
	 * deleted after the uninstall procedure.
	 *
	 * @return boolean
	 */
	public static function uninstall() {
		/**
		 * We want to be sure that the user has not deactivated the
		 * multisite because we need to use switch_to_blog and
		 * restore_current_blog
		 */
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$sql_cache = MslsSqlCacher::create( __CLASS__, __METHOD__ );
			$blogs     = ( new BlogsInNetworkQuery( $sql_cache ) )();

			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog->blog_id );
				self::cleanup();
				restore_current_blog();
			}
		}

		return self::cleanup();
	}

	/**
	 * Cleanup the options
	 *
	 * Removes all values of the current blogs which are stored in the
	 * options-table and returns true if it was successful.
	 *
	 * @return boolean
	 */
	public static function cleanup() {
		if ( delete_option( 'msls' ) ) {
			$sql_cache = MslsSqlCacher::create( __CLASS__, __METHOD__ );
			return ( new CleanupOptionsQuery( $sql_cache ) )();
		}

		return false;
	}
}
