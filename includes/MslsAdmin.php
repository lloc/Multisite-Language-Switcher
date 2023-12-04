<?php

namespace lloc\Msls;

use lloc\Msls\Component\Input\Checkbox;
use lloc\Msls\Component\Input\Group;
use lloc\Msls\Component\Input\Label;
use lloc\Msls\Component\Input\Text;
use lloc\Msls\Component\Input\Select;

/**
 * Administration of the options
 *
 * @package Msls
 */
class MslsAdmin extends MslsMain {

	public const MAX_REFERENCE_USERS = 100;

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
			$collection = msls_blog_collection();

			$obj = new static( $options, $collection );

			MslsRegistry::set_object( __CLASS__, $obj );

			/**
			 * Override the capabilities needed for the plugin's settings
			 *
			 * @param string $capability
			 *
			 * @since 2.0
			 *
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

		if ( 'rewrite' === $parts[0] ) {
			return $this->render_rewrite( $parts[1] );
		}

		$checkboxes = [
			'activate_autocomplete'   => __( 'Activate experimental autocomplete inputs', 'multisite-language-switcher' ),
			'activate_content_import' => __( 'Activate the content import functionality', 'multisite-language-switcher' ),
			'sort_by_description'     => __( 'Sort languages by description', 'multisite-language-switcher' ),
			'exclude_current_blog'    => __( 'Exclude this blog from output', 'multisite-language-switcher' ),
			'only_with_translation'   => __( 'Show only links with a translation', 'multisite-language-switcher' ),
			'output_current_blog'     => __( 'Display link to the current language', 'multisite-language-switcher' ),
			'content_filter'          => __( 'Add hint for available translations', 'multisite-language-switcher' ),
		];

		if ( isset ( $checkboxes[ $method ] ) ) {
			echo ( new Group() )->add( new Checkbox( $method, $this->options->$method ) )->add( new Label( $method, $checkboxes[ $method ] ) )->render();
		} else {
			$value = ! empty( $this->options->$method ) ? $this->options->$method : '';
			echo ( new Text( $method, $value ) )->render();
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
				__( 'Multisite Language Switcher is almost ready. You must <a href="%s">complete the configuration process</a>.', 'multisite-language-switcher' ),
				esc_url( admin_url( $this->get_options_page_link() ) )
			);
		} elseif ( 1 == count( $this->options->get_available_languages() ) ) {
			$message = sprintf(
				__( 'There are no language files installed. You can <a href="%s">manually install some language files</a> or you could use a <a href="%s">plugin</a> to download these files automatically.', 'multisite-language-switcher' ),
				esc_url( 'http://codex.wordpress.org/Installing_WordPress_in_Your_Language#Manually_Installing_Language_Files' ),
				esc_url( 'http://wordpress.org/plugins/wp-native-dashboard/' )
			);
		}

		return MslsPlugin::message_handler( $message, 'updated fade' );
	}

	/**
	 * Render the options-page
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
		$icon_type = $this->options->get_icon_type();

		$arr = [];
		foreach ( $this->collection->get_plugin_active_blogs() as $blog ) {
			$admin_url = get_admin_url( $blog->userblog_id, $this->get_options_page_link() );
			$current   = $blog->userblog_id == $this->collection->get_current_blog_id() ? ' class="current"' : '';

			$arr[] = sprintf( '<a href="%1$s"%2$s>%3$s</a>', $admin_url, $current, $blog->get_title( $icon_type ) );
		}

		return empty( $arr ) ? '' : sprintf( '<ul class="subsubsub"><li>%s</li></ul>', implode( ' | </li><li>', $arr ) );
	}

	/**
	 * Register the form-elements
	 *
	 * @codeCoverageIgnore
	 */
	public function register() {
		register_setting( 'msls', 'msls', [ $this, 'validate' ] );

		$sections = [
			'language_section' => __( 'Language Settings', 'multisite-language-switcher' ),
			'main_section'     => __( 'Main Settings', 'multisite-language-switcher' ),
			'advanced_section' => __( 'Advanced Settings', 'multisite-language-switcher' ),
		];

		global $wp_rewrite;
		if ( $wp_rewrite->using_permalinks() ) {
			$sections['rewrites_section'] = __( 'Rewrites Settings', 'multisite-language-switcher' );
		}

		foreach ( $sections as $id => $title ) {
			add_settings_section( $id, $title, [ $this, $id ], __CLASS__ );
		}

		/**
		 * Lets you add your own settings section
		 *
		 * @param string $page
		 *
		 * @since 1.0
		 *
		 */
		do_action( 'msls_admin_register', __CLASS__ );
	}

	/**
	 * Registers the fields in the language_section
	 *
	 * Returns the number of added fields
	 *
	 * @return int
	 */
	public function language_section(): int {
		$map = [ 'blog_language' => __( 'Blog Language', 'multisite-language-switcher' ) ];

		return $this->add_settings_fields( $map, 'language_section' );
	}

	/**
	 * Registers the fields in the main_section
	 *
	 * Returns the number of added fields
	 *
	 * @return int
	 */
	public function main_section(): int {
		$map = [
			'display'               => __( 'Display', 'multisite-language-switcher' ),
			'admin_display'         => __( 'Admin Display', 'multisite-language-switcher' ),
			'sort_by_description'   => __( 'Sort languages', 'multisite-language-switcher' ),
			'output_current_blog'   => __( 'Current language link', 'multisite-language-switcher' ),
			'only_with_translation' => __( 'Translation links', 'multisite-language-switcher' ),
			'description'           => __( 'Description', 'multisite-language-switcher' ),
			'before_output'         => __( 'Text/HTML before the list', 'multisite-language-switcher' ),
			'after_output'          => __( 'Text/HTML after the list', 'multisite-language-switcher' ),
			'before_item'           => __( 'Text/HTML before each item', 'multisite-language-switcher' ),
			'after_item'            => __( 'Text/HTML after each item', 'multisite-language-switcher' ),
			'content_filter'        => __( 'Available translations hint', 'multisite-language-switcher' ),
			'content_priority'      => __( 'Hint priority', 'multisite-language-switcher' ),
		];

		return $this->add_settings_fields( $map, 'main_section' );
	}

	/**
	 * Registers the fields in the advanced_section
	 *
	 * Returns the number of added fields
	 *
	 * @return int
	 */
	public function advanced_section(): int {
		$map = [
			'activate_autocomplete'   => __( 'Autocomplete', 'multisite-language-switcher' ),
			'image_url'               => __( 'Custom URL for flag-images', 'multisite-language-switcher' ),
			'reference_user'          => __( 'Reference user', 'multisite-language-switcher' ),
			'exclude_current_blog'    => __( 'Exclude blog', 'multisite-language-switcher' ),
			'activate_content_import' => __( 'Content import', 'multisite-language-switcher' ),
		];

		return $this->add_settings_fields( $map, 'advanced_section' );
	}

	/**
	 * Registers the fields in the rewrites_section
	 *
	 * Returns the number of added fields
	 *
	 * @return int
	 */
	public function rewrites_section(): int {
		$map = [];
		foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $key => $object ) {
			$map["rewrite_{$key}"] = sprintf( __( '%s Slug', 'multisite-language-switcher' ), $object->label );
		}

		return $this->add_settings_fields( $map, 'rewrites_section' );
	}

	/**
	 * @param array $map
	 * @param string $section
	 *
	 * @return int
	 */
	protected function add_settings_fields( array $map, string $section ): int {
		foreach ( $map as $id => $title ) {
			add_settings_field( $id, $title, [ $this, $id ], __CLASS__, $section, [ 'label_for' => $id ] );
		}

		/**
		 * Lets you add your own field to the section
		 *
		 * @param string $page
		 * @param string $section
		 *
		 * @since 2.4.4
		 *
		 */
		do_action( "msls_admin_{$section}", __CLASS__, $section );

		return count( $map );
	}

	/**
	 * Shows the select-form-field 'blog_language'
	 */
	public function blog_language() {
		$languages = $this->options->get_available_languages();
		$selected  = get_locale();

		echo ( new Select( 'blog_language', $languages, $selected ) )->render();
	}

	/**
	 * Shows the select-form-field 'display'
	 */
	public function display() {
		echo ( new Select( 'display', MslsLink::get_types_description(), $this->options->display ) )->render();
	}

	/**
	 * Shows the select-form-field 'admin_display'
	 */
	public function admin_display() {
		echo ( new Select( 'admin_display', array( 'flag' => __( 'Flag', 'multisite-language-switcher' ), 'label' => __( 'Label', 'multisite-language-switcher' ) ), $this->options->admin_display ) )->render();
	}

	/**
	 * Shows the select-form-field 'reference_user'
	 */
	public function reference_user() {
		$users = [];

		foreach ( ( array ) apply_filters( 'msls_reference_users', $this->collection->get_users() ) as $user ) {
			$users[ $user->ID ] = $user->user_nicename;
		}

		if ( count( $users ) > self::MAX_REFERENCE_USERS ) {
			$users = array_slice( $users, 0, self::MAX_REFERENCE_USERS, true );

			$message = sprintf(
				__( 'Multisite Language Switcher: Collection for reference user has been truncated because it exceeded the maximum of %s users. Please, use the hook "msls_reference_users" to filter the result before!', 'multisite-language-switcher' ),
				self::MAX_REFERENCE_USERS
			);
			trigger_error( $message );
		}

		echo ( new Select( 'reference_user', $users, $this->options->reference_user ) )->render();
	}

	/**
	 * The description for the current blog
	 *
	 * The language will be used ff there is no description.
	 */
	public function description() {
		echo ( new Text( 'description', $this->options->description, '40' ) )->render();
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
		 *
		 * @param array $arr
		 *
		 * @since 1.0
		 *
		 */
		$arr = (array) apply_filters( 'msls_admin_validate', $arr );

		$arr['display'] = intval( $arr['display'] ?? 0 );
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
