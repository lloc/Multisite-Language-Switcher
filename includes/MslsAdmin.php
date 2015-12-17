<?php
/**
 * MslsAdmin
 * @author Dennis Ploetner <re@lloc.de>
 * @since 0.9.8
 */

/**
 * Administration of the options
 * @package Msls
 */
class MslsAdmin extends MslsMain {

	/**
	 * Init
	 * @return MslsAdmin
	 */
	public static function init() {
		$obj = new self();

		if ( current_user_can( 'manage_options' ) ) {
			$title = __( 'Multisite Language Switcher', 'multisite-language-switcher' );
			add_options_page( $title, $title, 'manage_options', __CLASS__, array( $obj, 'render' ) );

			add_action( 'admin_init',    array( $obj, 'register' ) );
			add_action( 'admin_notices', array( $obj, 'has_problems' ) );

			add_filter( 'msls_admin_validate', array( $obj, 'set_blog_language' ) );
		}

		return $obj;
	}

	/**
	 * There is something wrong? Here comes the message...
	 * @return bool
	 */
	public function has_problems() {
		$message = '';
		$options = MslsOptions::instance();

		if ( 1 == count( $options->get_available_languages() ) ) {
			$message = sprintf(
				__( 'There are no language files installed. You can <a href="%s">manually install some language files</a> or you could use a <a href="%s">plugin</a> to download these files automatically.' ),
				esc_url( 'http://codex.wordpress.org/Installing_WordPress_in_Your_Language#Manually_Installing_Language_Files' ),
				esc_url( 'http://wordpress.org/plugins/wp-native-dashboard/' )
			);
		}
		elseif ( $options->is_empty() ) {
			$message = sprintf(
				__( 'Multisite Language Switcher is almost ready. You must <a href="%s">complete the configuration process</a>.' ),
				esc_url( admin_url( '/options-general.php?page=MslsAdmin' ) )
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
			'<p class="submit"><input name="Submit" type="submit" class="button-primary" value="%s" /></p></form></div>',
			( MslsOptions::instance()->is_empty() ? __( 'Configure', 'multisite-language-switcher' ) : __( 'Update', 'multisite-language-switcher' ) )
		);
	}

	/**
	 * Create a submenu which contains links to all blogs of the current user
	 * @return string
	 */
	public function subsubsub() {
		$arr = array();

		$blogs = MslsBlogCollection::instance();
		foreach ( $blogs->get_plugin_active_blogs() as $blog ) {
			$arr[] = sprintf(
				'<a href="%s"%s>%s / %s</a>',
				get_admin_url( $blog->userblog_id, '/options-general.php?page=MslsAdmin' ),
				( $blog->userblog_id == $blogs->get_current_blog_id() ? ' class="current"' : '' ),
				$blog->blogname,
				$blog->get_description()
			);
		}

		return(
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
		register_setting( 'msls', 'msls', array( $this, 'validate' ) );

		add_settings_section( 'language_section', __( 'Language Settings', 'multisite-language-switcher' ), array( $this, 'language_section' ), __CLASS__ );
		add_settings_section( 'main_section', __( 'Main Settings', 'multisite-language-switcher' ), array( $this, 'main_section' ), __CLASS__ );
		add_settings_section( 'advanced_section', __( 'Advanced Settings', 'multisite-language-switcher' ), array( $this, 'advanced_section' ), __CLASS__ );

		/**
		 * Lets you add your own settings section
		 * @since 1.0
		 * @param string $page
		 */
		do_action( 'msls_admin_register', __CLASS__ );
	}

	/**
	 * Register the fields in the language_section
	 * @codeCoverageIgnore
	 */
	public function language_section() {
		add_settings_field( 'blog_language', __( 'Blog Language', 'multisite-language-switcher' ), array( $this, 'blog_language' ), __CLASS__, 'language_section' );
		add_settings_field( 'admin_language', __( 'Admin Language', 'multisite-language-switcher' ), array( $this, 'admin_language' ), __CLASS__, 'language_section' );

		/**
		 * Lets you add your own field to the language section
		 * @since 1.0
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
		add_settings_field( 'display', __( 'Display', 'multisite-language-switcher' ), array( $this, 'display' ), __CLASS__, 'main_section' );
		add_settings_field( 'sort_by_description', __( 'Sort output by description', 'multisite-language-switcher' ), array( $this, 'sort_by_description' ), __CLASS__, 'main_section' );
		add_settings_field( 'output_current_blog', __( 'Display link to the current language', 'multisite-language-switcher' ), array( $this, 'output_current_blog' ), __CLASS__, 'main_section' );
		add_settings_field( 'only_with_translation', __( 'Show only links with a translation', 'multisite-language-switcher' ), array( $this, 'only_with_translation' ), __CLASS__, 'main_section' );
		add_settings_field( 'description', __( 'Description', 'multisite-language-switcher' ), array( $this, 'description' ), __CLASS__, 'main_section' );
		add_settings_field( 'before_output', __( 'Text/HTML before the list', 'multisite-language-switcher' ), array( $this, 'before_output' ), __CLASS__, 'main_section' );
		add_settings_field( 'after_output', __( 'Text/HTML after the list', 'multisite-language-switcher' ), array( $this, 'after_output' ), __CLASS__, 'main_section' );
		add_settings_field( 'before_item', __( 'Text/HTML before each item', 'multisite-language-switcher' ), array( $this, 'before_item' ), __CLASS__, 'main_section' );
		add_settings_field( 'after_item', __( 'Text/HTML after each item', 'multisite-language-switcher' ), array( $this, 'after_item' ), __CLASS__, 'main_section' );
		add_settings_field( 'content_filter', __( 'Add hint for available translations', 'multisite-language-switcher' ), array( $this, 'content_filter' ), __CLASS__, 'main_section' );
		add_settings_field( 'content_priority', __( 'Hint priority', 'multisite-language-switcher' ), array( $this, 'content_priority' ), __CLASS__, 'main_section' );

		/**
		 * Lets you add your own field to the main section
		 * @since 1.0
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
		add_settings_field( 'activate_autocomplete', __( 'Activate experimental autocomplete inputs', 'multisite-language-switcher' ), array( $this, 'activate_autocomplete' ), __CLASS__, 'advanced_section' );
		add_settings_field( 'image_url', __( 'Custom URL for flag-images', 'multisite-language-switcher' ), array( $this, 'image_url' ), __CLASS__, 'advanced_section' );
		add_settings_field( 'reference_user', __( 'Reference user', 'multisite-language-switcher' ), array( $this, 'reference_user' ), __CLASS__, 'advanced_section' );
		add_settings_field( 'exclude_current_blog', __( 'Exclude this blog from output', 'multisite-language-switcher' ), array( $this, 'exclude_current_blog' ), __CLASS__, 'advanced_section' );

		/**
		 * Lets you add your own field to the advanced section
		 * @since 1.0
		 * @param string $page
		 * @param string $section
		 */
		do_action( 'msls_admin_advanced_section', __CLASS__, 'advanced_section' );
	}

	/**
	 * Shows the select-form-field 'blog_language'
	 */
	public function blog_language() {
		echo $this->render_select(
			'blog_language',
			MslsOptions::instance()->get_available_languages(),
			get_option( 'WPLANG', 'en_US' )
		);
	}

	/**
	 * Shows the select-form-field 'admin_language'
	 */
	public function admin_language() {
		$options = MslsOptions::instance();
		echo $this->render_select(
			'admin_language',
			$options->get_available_languages(),
			$options->admin_language
		);
	}

	/**
	 * Shows the select-form-field 'display'
	 */
	public function display() {
		echo $this->render_select(
			'display',
			MslsLink::get_types_description(),
			MslsOptions::instance()->display
		);
	}

	/**
	 * Shows the select-form-field 'reference_user'
	 */
	public function reference_user() {
		$users = array();

		foreach ( MslsBlogCollection::instance()->get_users() as $user ) {
			$users[ $user->ID ] = $user->user_nicename;
		}

		echo $this->render_select( 'reference_user', $users, MslsOptions::instance()->reference_user );
	}

	/**
	 * Activate autocomplete
	 *
	 * You can decide if you want to activate the experimental autocomplete
	 * input fields in the backend instead of the traditional select-menus.
	 */
	public function activate_autocomplete() {
		echo $this->render_checkbox( 'activate_autocomplete' );
	}

	/**
	 * Show sort_by_description-field
	 *
	 * You can decide that the ouput will be sorted by the description. If not
	 * the output will be sorted by the language-code.
	 */
	public function sort_by_description() {
		echo $this->render_checkbox( 'sort_by_description' );
	}

	/**
	 * Exclude the current blog
	 *
	 * You can exclude a blog explicitly. All your settings will be safe but the
	 * plugin will ignore this blog while this option is active.
	 */
	public function exclude_current_blog() {
		echo $this->render_checkbox( 'exclude_current_blog' );
	}

	/**
	 * Show only a link  if a translation is available
	 *
	 * Some user requested this feature. Shows only links to available
	 * translations.
	 */
	public function only_with_translation() {
		echo $this->render_checkbox( 'only_with_translation' );
	}

	/**
	 * Show a link to the current blog
	 *
	 * Some user requested this feature. If active the plugin will place also a
	 * link to the current blog.
	 */
	public function output_current_blog() {
		echo $this->render_checkbox( 'output_current_blog' );
	}

	/**
	 * The description for the current blog
	 *
	 * The language will be used ff there is no description.
	 */
	public function description() {
		echo $this->render_input( 'description', '40' );
	}

	/**
	 * A String which will be placed before the output of the list
	 */
	public function before_output() {
		echo $this->render_input( 'before_output' );
	}

	/**
	 * A String which will be placed after the output of the list
	 */
	public function after_output() {
		echo $this->render_input( 'after_output' );
	}

	/**
	 * A String which will be placed before every item of the list
	 */
	public function before_item() {
		echo $this->render_input( 'before_item' );
	}

	/**
	 * A String which will be placed after every item of the list
	 */
	public function after_item() {
		echo $this->render_input( 'after_item' );
	}

	/**
	 * The output can be placed after the_content
	 */
	public function content_filter() {
		echo $this->render_checkbox( 'content_filter' );
	}

	/**
	 * If the output in the_content is active you can set the priority too
	 *
	 * Default is 10. But may be there are other plugins active and you run into
	 * trouble. So you can decide a higher (from 1) or a lower (to 100) priority
	 * for the output
	 */
	public function content_priority() {
		$temp     = array_merge( range( 1, 10 ), array( 20, 50, 100 ) );
		$arr      = array_combine( $temp, $temp );
		$options  = MslsOptions::instance();
		$selected = (
			empty( $options->content_priority ) ?
			10 :
			$options->content_priority
		);

		echo $this->render_select( 'content_priority', $arr, $selected );
	}

	/**
	 * Alternative image-url
	 * @todo This is a value of a directory-url which should be more clear
	 */
	public function image_url() {
		echo $this->render_input( 'image_url' );
	}

	/**
	 * Render form-element (checkbox)
	 * @param string $key Name and ID of the form-element
	 * @return string
	 */
	public function render_checkbox( $key ) {
		return sprintf(
			'<input type="checkbox" id="%1$s" name="msls[%1$s]" value="1" %2$s/>',
			$key,
			checked( 1, MslsOptions::instance()->$key, false )
		);
	}

	/**
	 * Render form-element (text-input)
	 * @param string $key Name and ID of the form-element
	 * @param string $size Size-attribute of the input-field
	 * @return string
	 */
	public function render_input( $key, $size = '30' ) {
		return sprintf(
			'<input id="%1$s" name="msls[%1$s]" value="%2$s" size="%3$s"/>',
			$key,
			esc_attr( MslsOptions::instance()->$key ),
			$size
		);
	}

	/**
	 * Render form-element (select)
	 * @uses selected
	 * @param string $key Name and ID of the form-element
	 * @param array $arr Options as associative array
	 * @param string $selected Values which should be selected
	 * @return string
	 */
	public function render_select( $key, array $arr, $selected = '' ) {
		$options = array();

		foreach ( $arr as $value => $description ) {
			$options[] = sprintf(
				'<option value="%s" %s>%s</option>',
				$value,
				selected( $value, $selected, false ),
				$description
			);
		}

		return sprintf(
			'<select id="%1$s" name="msls[%1$s]">%2$s</select>',
			$key,
			implode( '', $options )
		);
	}

	/**
	 * Validates input before saving it
	 * @param array $arr Values of the submitted form
	 * @return array Validated input
	 */
	public function validate( array $arr ) {
		/**
		 * Returns custom filtered input array
		 * @since 1.0
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
	 * @param array $arr
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
