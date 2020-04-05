<?php
/**
 * MslsAdmin
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

namespace lloc\Msls;

use lloc\Msls\Component\Input\Checkbox;
use lloc\Msls\Component\Input\Group;
use lloc\Msls\Component\Input\Label;
use lloc\Msls\Component\Input\Text;
use lloc\Msls\Component\Input\Select;

/**
 * Administration of the options
 * @package Msls
 */
class MslsAdmin extends MslsMain {

	/**
	 * Factory
	 *
	 * @codeCoverageIgnore
	 *
	 * @return MslsAdmin
	 */
	public static function init() {
		if ( ! ( $obj = MslsRegistry::get_object( __CLASS__ ) ) ) {
			$options    = MslsOptions::instance();
			$collection = MslsBlogCollection::instance();

			$obj = new static( $options, $collection );

			MslsRegistry::set_object( __CLASS__, $obj );

			/**
			 * Override the capabilities needed for the plugin's settings
			 *
			 * @since 2.0
			 *
			 * @param string $capability
			 */
			$caps = apply_filters( 'msls_admin_caps', 'manage_options' );
			if ( current_user_can( $caps ) ) {
				$title = __( 'Multisite Language Switcher', 'multisite-language-switcher' );
				add_options_page( $title, $title, 'manage_options', $obj->get_menu_slug(), [ $obj, 'render' ] );

				add_action( 'admin_init', [ $obj, 'register' ] );
				add_action( 'admin_notices', [ $obj, 'has_problems' ] );

				add_filter( 'msls_admin_validate', [ $obj, 'set_blog_language' ] );
			}
		}

		return $obj;
	}

	/**
	 * Let's do this simple
	 *
	 * @return string
	 */
	public function get_menu_slug() {
		return 'MslsAdmin';
	}

	/**
	 * Get's the link for the switcher-settings in the wp-admin
	 *
	 * @return string
	 */
	public function get_options_page_link() {
		return sprintf( '/options-general.php?page=%s', $this->get_menu_slug() );
	}

	/**
	 * You can use every method of the decorated object
	 *
	 * @param string $method
	 * @param mixed $args
	 *
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		$parts = explode( '_', $method, 2 );
		if ( 2 == count( $parts ) ) {
			switch ( $parts[0] ) {
				case 'rewrite':
					return $this->render_rewrite( $parts[1] );
					break;
				case 'text':
					$key   = $parts[1];
					$value = ! empty( $this->options->$key ) ? $this->options->$key : '';
					echo ( new Text( $key, $value ) )->render();
					break;
			}
		}
	}

	/**
	 * There is something wrong? Here comes the message...
	 * @return bool
	 */
	public function has_problems() {
		$message = '';

		if ( $this->options->is_empty() ) {
			$message = sprintf(
				__( 'Multisite Language Switcher is almost ready. You must complete the configuration process</a>.' ),
				esc_url( admin_url( $this->get_options_page_link() ) )
			);
		} elseif ( 1 == count( $this->options->get_available_languages() ) ) {
			$message = sprintf(
				__( 'There are no language files installed. You can <a href="%s">manually install some language files</a> or you could use a <a href="%s">plugin</a> to download these files automatically.' ),
				esc_url( 'http://codex.wordpress.org/Installing_WordPress_in_Your_Language#Manually_Installing_Language_Files' ),
				esc_url( 'http://wordpress.org/plugins/wp-native-dashboard/' )
			);
		}

		return MslsPlugin::message_handler( $message, 'updated fade' );
	}

	/**
	 * Render the options-page
	 * @codeCoverageIgnore
	 */
	public function render() {
		printf(
			'<div class="wrap"><div class="icon32" id="icon-options-general"><br/></div><h1>%s</h1>%s<br class="clear"/><form action="options.php" method="post"><p>%s</p>',
			__( 'Multisite Language Switcher Options', 'multisite-language-switcher' ),
			$this->subsubsub(),
			__( 'To achieve maximum flexibility, you have to configure each blog separately.', 'multisite-language-switcher' )
		);

		settings_fields( 'msls' );
		do_settings_sections( __CLASS__ );

		printf(
			'<p class="submit"><input name="Submit" type="submit" class="button button-primary" value="%s" /></p></form></div>',
			( $this->options->is_empty() ? __( 'Configure', 'multisite-language-switcher' ) : __( 'Update', 'multisite-language-switcher' ) )
		);
	}


	/**
	 * Create a submenu which contains links to all blogs of the current user
	 * @return string
	 */
	public function subsubsub() {
		$arr = [];

		foreach ( $this->collection->get_plugin_active_blogs() as $blog ) {
			$arr[] = sprintf(
				'<a href="%s"%s>%s / %s</a>',
				get_admin_url( $blog->userblog_id, $this->get_options_page_link() ),
				( $blog->userblog_id == $this->collection->get_current_blog_id() ? ' class="current"' : '' ),
				$blog->blogname,
				$blog->get_description()
			);
		}

		return (
			empty( $arr ) ?
			'' :
			sprintf(
				'<ul class="subsubsub"><li>%s</li></ul>',
				implode( ' | </li><li>', $arr )
			)
		);
	}

	/**
	 * Register the form-elements
	 * @codeCoverageIgnore
	 */
	public function register() {
		register_setting( 'msls', 'msls', [ $this, 'validate' ] );

		add_settings_section( 'language_section', __( 'Language Settings', 'multisite-language-switcher' ), array(
			$this,
			'language_section'
		), __CLASS__ );
		add_settings_section( 'main_section', __( 'Main Settings', 'multisite-language-switcher' ), array(
			$this,
			'main_section'
		), __CLASS__ );
		add_settings_section( 'advanced_section', __( 'Advanced Settings', 'multisite-language-switcher' ), array(
			$this,
			'advanced_section'
		), __CLASS__ );

		global $wp_rewrite;
		if ( $wp_rewrite->using_permalinks() ) {
			add_settings_section( 'rewrites_section', __( 'Rewrites Settings', 'multisite-language-switcher' ), array(
				$this,
				'rewrites_section'
			), __CLASS__ );
		}

		/**
		 * Lets you add your own settings section
		 * @since 1.0
		 *
		 * @param string $page
		 */
		do_action( 'msls_admin_register', __CLASS__ );
	}

	/**
	 * Register the fields in the language_section
	 * @codeCoverageIgnore
	 */
	public function language_section() {
		add_settings_field( 'blog_language', __( 'Blog Language', 'multisite-language-switcher' ), array(
			$this,
			'blog_language'
		), __CLASS__, 'language_section', array( 'label_for' => 'blog_language' ) );

		/**
		 * Lets you add your own field to the language section
		 * @since 1.0
		 *
		 * @param string $page
		 * @param string $section
		 */
		do_action( 'msls_admin_language_section', __CLASS__, 'language_section' );
	}

	/**
	 * Register the fields in the main_section
	 * @codeCoverageIgnore
	 */
	public function main_section() {
		add_settings_field( 'display', __( 'Display', 'multisite-language-switcher' ), array(
			$this,
			'display'
		), __CLASS__, 'main_section', array( 'label_for' => 'display' ) );
		add_settings_field( 'sort_by_description', __( 'Sort languages', 'multisite-language-switcher' ), array(
			$this,
			'sort_by_description'
		), __CLASS__, 'main_section' );
		add_settings_field( 'output_current_blog', __( 'Current language link', 'multisite-language-switcher' ), array(
			$this,
			'output_current_blog'
		), __CLASS__, 'main_section' );
		add_settings_field( 'only_with_translation', __( 'Translation links', 'multisite-language-switcher' ), array(
			$this,
			'only_with_translation'
		), __CLASS__, 'main_section' );
		add_settings_field( 'description', __( 'Description', 'multisite-language-switcher' ), array(
			$this,
			'description'
		), __CLASS__, 'main_section', array( 'label_for' => 'description' ) );
		add_settings_field( 'before_output', __( 'Text/HTML before the list', 'multisite-language-switcher' ), array(
			$this,
			'text_before_output'
		), __CLASS__, 'main_section', array( 'label_for' => 'before_output' ) );
		add_settings_field( 'after_output', __( 'Text/HTML after the list', 'multisite-language-switcher' ), array(
			$this,
			'text_after_output'
		), __CLASS__, 'main_section', array( 'label_for' => 'after_output' ) );
		add_settings_field( 'before_item', __( 'Text/HTML before each item', 'multisite-language-switcher' ), array(
			$this,
			'text_before_item'
		), __CLASS__, 'main_section', array( 'label_for' => 'before_item' ) );
		add_settings_field( 'after_item', __( 'Text/HTML after each item', 'multisite-language-switcher' ), array(
			$this,
			'text_after_item'
		), __CLASS__, 'main_section', array( 'label_for' => 'after_item' ) );
		add_settings_field( 'content_filter', __( 'Available translations hint', 'multisite-language-switcher' ), array(
			$this,
			'content_filter'
		), __CLASS__, 'main_section' );
		add_settings_field( 'content_priority', __( 'Hint priority', 'multisite-language-switcher' ), array(
			$this,
			'content_priority'
		), __CLASS__, 'main_section', array( 'label_for' => 'content_priority' ) );

		/**
		 * Lets you add your own field to the main section
		 * @since 1.0
		 *
		 * @param string $page
		 * @param string $section
		 */
		do_action( 'msls_admin_main_section', __CLASS__, 'main_section' );
	}

	/**
	 * Register the fields in the advanced_section
	 * @codeCoverageIgnore
	 */
	public function advanced_section() {
		add_settings_field( 'activate_autocomplete', __( 'Autocomplete', 'multisite-language-switcher' ), array(
			$this,
			'activate_autocomplete'
		), __CLASS__, 'advanced_section' );
		add_settings_field( 'image_url', __( 'Custom URL for flag-images', 'multisite-language-switcher' ), array(
			$this,
			'text_image_url'
		), __CLASS__, 'advanced_section', array( 'label_for' => 'image_url' ) );
		add_settings_field( 'reference_user', __( 'Reference user', 'multisite-language-switcher' ), array(
			$this,
			'reference_user'
		), __CLASS__, 'advanced_section', array( 'label_for' => 'reference_user' ) );
		add_settings_field( 'exclude_current_blog', __( 'Exclude blog', 'multisite-language-switcher' ), array(
			$this,
			'exclude_current_blog'
		), __CLASS__, 'advanced_section' );
		add_settings_field( 'activate_content_import', __( 'Content import', 'multisite-language-switcher' ), array(
			$this,
			'activate_content_import'
		), __CLASS__, 'advanced_section' );

		/**
		 * Lets you add your own field to the advanced section
		 * @since 1.0
		 *
		 * @param string $page
		 * @param string $section
		 */
		do_action( 'msls_admin_advanced_section', __CLASS__, 'advanced_section' );
	}

	/**
	 * Register the fields in the rewrites_section
	 * @since 1.1
	 * @codeCoverageIgnore
	 */
	public function rewrites_section() {
		foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $key => $object ) {
			$title = sprintf( __( '%s Slug', 'multisite-language-switcher' ), $object->label );
			add_settings_field(
				"rewrite_{$key}",
				$title,
				[ $this, "rewrite_{$key}" ],
				__CLASS__,
				'rewrites_section',
				array( 'label_for' => "rewrite_{$key}" )
			);
		}

		/**
		 * Lets you add your own field to the rewrites section
		 *
		 * @param string $page
		 * @param string $section
		 */
		do_action( 'msls_admin_rewrites_section', __CLASS__, 'rewrites_section' );
	}

	/**
	 * Shows the select-form-field 'blog_language'
	 */
	public function blog_language() {
		$languages = $this->options->get_available_languages();
		$selected  = get_locale();

		echo ( new Select('blog_language', $languages, $selected ) )->render();
	}

	/**
	 * Shows the select-form-field 'display'
	 */
	public function display() {
		echo ( new Select('display', MslsLink::get_types_description(), $this->options->display ) )->render();
	}

	/**
	 * Shows the select-form-field 'reference_user'
	 */
	public function reference_user() {
		$users = [];

		foreach ( $this->collection->get_users() as $user ) {
			$users[ $user->ID ] = $user->user_nicename;
		}

		echo ( new Select( 'reference_user', $users, $this->options->reference_user ) )->render();
	}

	/**
	 * render
	 *
	 * You can decide if you want to activate the experimental autocomplete
	 * input fields in the backend instead of the traditional select-menus.
	 */
	public function activate_autocomplete() {
		$key   = 'activate_autocomplete';
		$text  = __( 'Activate experimental autocomplete inputs', 'multisite-language-switcher' );

		echo ( new Group() )->add( new Checkbox( $key, $this->options->$key ) )->add( new Label( $key, $text ) )->render();
	}

	/**
	 * Activate content import
	 *
	 * You can decide if you want to activate the content import functionality
	 * in the backend instead of the traditional select-menus.
	 */
	public function activate_content_import() {
		$key  = 'activate_content_import';
		$text = __( 'Activate the content import functionality', 'multisite-language-switcher' );

		echo ( new Group() )->add( new Checkbox( $key, $this->options->$key ) )->add( new Label( $key, $text ) )->render();
	}

	/**
	 * Show sort_by_description-field
	 *
	 * You can decide that the output will be sorted by the description. If not
	 * the output will be sorted by the language-code.
	 */
	public function sort_by_description() {
		$key   = 'sort_by_description';
		$text  = __( 'Sort languages by description', 'multisite-language-switcher' );

		echo ( new Group() )->add( new Checkbox( $key, $this->options->$key ) )->add( new Label( $key, $text ) )->render();
	}

	/**
	 * Exclude the current blog
	 *
	 * You can exclude a blog explicitly. All your settings will be safe but the
	 * plugin will ignore this blog while this option is active.
	 */
	public function exclude_current_blog() {
		$key  = 'exclude_current_blog';
		$text =  __( 'Exclude this blog from output', 'multisite-language-switcher' );

		echo ( new Group() )->add( new Checkbox( $key, $this->options->$key ) )->add( new Label( $key, $text ) )->render();
	}

	/**
	 * Show only a link  if a translation is available
	 *
	 * Some user requested this feature. Shows only links to available
	 * translations.
	 */
	public function only_with_translation() {
		$key  = 'only_with_translation';
		$text = __( 'Show only links with a translation', 'multisite-language-switcher' );

		echo ( new Group() )->add( new Checkbox( $key, $this->options->$key ) )->add( new Label( $key, $text ) )->render();
	}

	/**
	 * Show a link to the current blog
	 *
	 * Some user requested this feature. If active the plugin will place also a
	 * link to the current blog.
	 */
	public function output_current_blog() {
		$key  = 'output_current_blog';
		$text = __( 'Display link to the current language', 'multisite-language-switcher' );

		echo ( new Group() )->add( new Checkbox( $key, $this->options->$key ) )->add( new Label( $key, $text ) )->render();
	}

	/**
	 * The description for the current blog
	 *
	 * The language will be used ff there is no description.
	 */
	public function description() {
		echo ( new Text('description', $this->options->description, '40' ) )->render();
	}

	/**
	 * The output can be placed after the_content
	 */
	public function content_filter() {
		$key  = 'content_filter';
		$text = __( 'Add hint for available translations', 'multisite-language-switcher' );

		echo ( new Group() )->add( new Checkbox( $key, $this->options->$key ) )->add( new Label( $key, $text ) )->render();
	}

	/**
	 * If the output in the_content is active you can set the priority too
	 *
	 * Default is 10. But may be there are other plugins active and you run into
	 * trouble. So you can decide a higher (from 1) or a lower (to 100) priority
	 * for the output
	 */
	public function content_priority() {
		$temp     = array_merge( range( 1, 10 ), [ 20, 50, 100 ] );
		$arr      = array_combine( $temp, $temp );
		$selected = empty( $this->options->content_priority ) ? 10 : $this->options->content_priority;

		echo ( new Select( 'content_priority', $arr, $selected ) )->render();
	}

	/**
	 * Rewrites slugs for registered post types
	 *
	 * @param string $key
	 */
	public function render_rewrite( $key ) {
		$rewrite = get_post_type_object( $key )->rewrite;

		$value = '';
		if ( true === $rewrite ) {
			$value = $key;
		} elseif ( ! empty( $rewrite['slug'] ) ) {
			$value = $rewrite['slug'];
		}

		echo ( new Text( "rewrite_{$key}", $value, 30, true ) )->render();
	}

	/**
	 * Validates input before saving it
	 *
	 * @param array $arr Values of the submitted form
	 *
	 * @return array Validated input
	 */
	public function validate( array $arr ) {
		/**
		 * Returns custom filtered input array
		 * @since 1.0
		 *
		 * @param array $arr
		 */
		$arr = apply_filters( 'msls_admin_validate', $arr );

		$arr['display'] = (
		isset( $arr['display'] ) ?
			(int) $arr['display'] :
			0
		);

		if ( isset( $arr['image_url'] ) ) {
			$arr['image_url'] = rtrim( esc_attr( $arr['image_url'] ), '/' );
		}

		return $arr;
	}

	/**
	 * Filter which sets the global blog language
	 *
	 * @param array $arr
	 *
	 * @return array
	 */
	public function set_blog_language( array $arr ) {
		if ( isset( $arr['blog_language'] ) ) {
			$blog_language = ( 'en_US' === $arr['blog_language'] ) ? '' : $arr['blog_language'];
			update_option( 'WPLANG', $blog_language );
			unset( $arr['blog_language'] );
		}

		return $arr;
	}

}
