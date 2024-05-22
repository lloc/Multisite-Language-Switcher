<?php
/**
 * MslsPlugin
 *
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

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
			add_filter( 'msls_get_output', array( __CLASS__, 'get_output' ) );

			add_action( 'widgets_init', array( $obj, 'init_widget' ) );
			add_filter( 'the_content', array( $obj, 'content_filter' ) );

			add_action( 'wp_head', array( __CLASS__, 'print_alternate_links' ) );

			if ( function_exists( 'register_block_type' ) ) {
				add_action( 'init', array( $obj, 'block_init' ) );
			}

			add_action( 'init', array( $obj, 'admin_bar_init' ) );
			add_action( 'admin_enqueue_scripts', array( $obj, 'custom_enqueue' ) );
			add_action( 'wp_enqueue_scripts', array( $obj, 'custom_enqueue' ) );

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

				if ( filter_has_var( INPUT_POST, 'action' ) ) {
					$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

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
					$href = 'https://wordpress.org/support/article/create-a-network/';
					$msg  = sprintf(
						__(
							'The Multisite Language Switcher needs the activation of the multisite-feature for working properly. Please read <a onclick="window.open(this.href); return false;" href="%s">this post</a> if you don\'t know the meaning.',
							'multisite-language-switcher'
						),
						$href
					);

					self::message_handler( $msg );
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
	 * @param $wp_admin_bar
	 *
	 * @return void
	 */
	public static function update_adminbar( \WP_Admin_Bar $wp_admin_bar ): void {
		$icon_type = msls_options()->get_icon_type();

		$blog_collection = msls_blog_collection();
		foreach ( $blog_collection->get_plugin_active_blogs() as $blog ) {
			$title = $blog->get_blavatar() . $blog->get_title( $icon_type );

			$wp_admin_bar->add_node(
				array(
					'id'    => 'blog-' . $blog->userblog_id,
					'title' => $title,
				)
			);
		}

		$blog = $blog_collection->get_current_blog();
		if ( is_object( $blog ) && method_exists( $blog, 'get_title' ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'    => 'site-name',
					'title' => $blog->get_title( $icon_type ),
				)
			);
		}
	}

	/**
	 * Callback for action wp_head
	 */
	public static function print_alternate_links() {
		echo self::get_output()->get_alternate_links(), PHP_EOL;
	}

	/**
	 * Filter for the_content()
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function content_filter( $content ) {
		if ( ! is_front_page() && is_singular() ) {
			$options = $this->options;

			if ( $options->is_content_filter() ) {
				$content .= $this->filter_string();
			}
		}

		return $content;
	}

	/**
	 * Create filterstring for msls_content_filter()
	 *
	 * @param string $pref
	 * @param string $post
	 *
	 * @return string
	 */
	public function filter_string( $pref = '<p id="msls">', $post = '</p>' ) {
		$obj    = MslsOutput::init();
		$links  = $obj->get( 1, true, true );
		$output = __( 'This post is also available in %s.', 'multisite-language-switcher' );

		if ( has_filter( 'msls_filter_string' ) ) {
			/**
			 * Overrides the string for the output of the translation hint
			 *
			 * @param string $output
			 * @param array $links
			 *
			 * @since 1.0
			 */
			$output = apply_filters( 'msls_filter_string', $output, $links );
		} else {
			$output = '';

			if ( count( $links ) > 1 ) {
				$last   = array_pop( $links );
				$output = sprintf(
					$output,
					sprintf(
						__( '%1$s and %2$s', 'multisite-language-switcher' ),
						implode( ', ', $links ),
						$last
					)
				);
			} elseif ( 1 == count( $links ) ) {
				$output = sprintf(
					$output,
					$links[0]
				);
			}
		}

		return ! empty( $output ) ? $pref . $output . $post : '';
	}

	/**
	 * Register block and shortcode.
	 *
	 * @return bool
	 */
	public function block_init() {
		if ( ! $this->options->is_excluded() ) {
			register_block_type( self::plugin_dir_path( 'js/msls-widget-block' ) );
			add_shortcode( 'sc_msls_widget', array( $this, 'block_render' ) );

			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function admin_bar_init() {
		if ( is_admin_bar_showing() ) {
			add_action( 'admin_bar_menu', array( __CLASS__, 'update_adminbar' ), 999 );

			return true;
		}

		return false;
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
			wp_enqueue_script( 'msls-autocomplete', self::plugins_url( "$folder/msls.js" ), array( 'jquery-ui-autocomplete' ), $ver );

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
	 * Register widget
	 *
	 * The widget will only be registered if the current blog is not
	 * excluded in the configuration of the plugin.
	 *
	 * @return boolean
	 */
	public function init_widget() {
		if ( ! $this->options->is_excluded() ) {
			register_widget( MslsWidget::class );

			return true;
		}

		return false;
	}

	/**
	 * Render widget output
	 *
	 * @return string
	 */
	public function block_render() {
		if ( ! $this->init_widget() ) {
			return '';
		}

		ob_start();
		the_widget( MslsWidget::class );
		$output = ob_get_clean();

		return $output;
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
			$cache = MslsSqlCacher::init( __CLASS__ )->set_params( __METHOD__ );

			$blogs = $cache->get_results(
				$cache->prepare(
					"SELECT blog_id FROM {$cache->blogs} WHERE blog_id != %d AND site_id = %d",
					$cache->blogid,
					$cache->siteid
				)
			);

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
			$cache = MslsSqlCacher::init( __CLASS__ )->set_params( __METHOD__ );
			$sql   = $cache->prepare(
				"DELETE FROM {$cache->options} WHERE option_name LIKE %s",
				'msls_%'
			);

			return (bool) $cache->query( $sql );
		}

		return false;
	}

	/**
	 * Get specific vars from $_POST and $_GET in a safe way
	 *
	 * @param array $list
	 *
	 * @return array
	 */
	public static function get_superglobals( array $list ) {
		$arr = array();

		foreach ( $list as $var ) {
			$arr[ $var ] = '';

			if ( filter_has_var( INPUT_POST, $var ) ) {
				$arr[ $var ] = filter_input( INPUT_POST, $var );
			} elseif ( filter_has_var( INPUT_GET, $var ) ) {
				$arr[ $var ] = filter_input( INPUT_GET, $var );
			}
		}

		return $arr;
	}
}
