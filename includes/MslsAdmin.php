<?php

/**
 * Administration of the options
 *
 * @package Msls
 * @subpackage Main
 */
class MslsAdmin extends MslsMain {
    
    /**
     * Init
     */
    public static function init() {
        wp_register_style( 'msls-styles', plugins_url( 'styles.css', MSLS_PLUGIN__FILE__ ), array(), MSLS_PLUGIN_VERSION );
        wp_enqueue_style ( 'msls-styles' );
        $obj = new self();
        add_options_page(
            __( 'Multisite Language Switcher', 'msls' ),
            __( 'Multisite Language Switcher', 'msls' ),
            'manage_options',
            __CLASS__,
            array( $obj, 'render' )
        );
        add_action( 'admin_init', array( $obj, 'register' ) );
		add_action( 'admin_notices', array( $obj, 'warning' ) );
    }

    public function warning() {
        if ( $this->options->is_empty() )
            echo '<div id="msls-warning" class="updated fade"><p>' .
                sprintf( __('Multisite Language Switcher is almost ready. You must <a href="%s">complete the configuration process</a>.'), 'options-general.php?page=MslsAdmin' ) .
                '</p></div>';
    }

    /**
     * Render the options-page
     */
    public function render() {
        printf(
            '<div class="wrap"><div class="icon32" id="icon-options-general"><br></div><h2>%s</h2>%s<form class="clear" action="options.php" method="post"><p>%s</p>',
            __( 'Multisite Language Switcher Options', 'msls' ),
            $this->subsubsub(),
            __( 'To achieve maximum flexibility, you have to configure each blog separately.', 'msls' )
        );
        settings_fields( 'msls' );
        do_settings_sections( __CLASS__ );
        printf(
            '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="%s" /></p></form></div>',
            ( $this->options->is_empty() ? __( 'Configure', 'msls' ) : __( 'Update', 'msls' ) )
        );
    }

    /**
     * Create a submenu which contains links to all blogs of the current user
     */
    protected function subsubsub() {
        $arr            = array();
        foreach ( $this->blogs->get_objects() as $id => $blog ) {
            if ( !$this->blogs->is_plugin_active( $blog->userblog_id ) )
                continue;
            $current = '';
            if ( $blog->userblog_id == $this->blogs->get_current_blog_id() )
                $current = ' class="current"';
            $arr[] = sprintf(
                '<a href="%s"%s>%s / %s</a>',
                get_admin_url( $blog->userblog_id, '/options-general.php?page=MslsAdmin' ),
                $current,
                $blog->blogname,
                $blog->get_description()
            );
        }
        return(
            !empty( $arr ) ?
            sprintf(
                '<ul class="subsubsub"><li>%s</li></ul>', 
                implode( ' | </li><li>', $arr )
            ) :
            ''
        );
    }

    /**
     * Register the form-elements
     */
    public function register() {
        register_setting( 'msls', 'msls', array( $this, 'validate' ) );
        add_settings_section(
            'section',
            __( 'Main Settings', 'msls' ),
            array( $this, 'section' ),
            __CLASS__
        );
        add_settings_field( 'display', __( 'Display', 'msls' ), array( $this, 'display' ), __CLASS__, 'section' );
        add_settings_field( 'sort_by_description', __( 'Sort output by description', 'msls' ), array( $this, 'sort_by_description' ), __CLASS__, 'section' );
        add_settings_field( 'exclude_current_blog', __( 'Exclude this blog from output', 'msls' ), array( $this, 'exclude_current_blog' ), __CLASS__, 'section' );
        add_settings_field( 'only_with_translation', __( 'Show only links with a translation', 'msls' ), array( $this, 'only_with_translation' ), __CLASS__, 'section' );
        add_settings_field( 'output_current_blog', __( 'Display link to the current language', 'msls' ), array( $this, 'output_current_blog' ), __CLASS__, 'section' );
        add_settings_field( 'description', __( 'Description', 'msls' ), array( $this, 'description' ), __CLASS__, 'section' );
        add_settings_field( 'before_output', __( 'Text/HTML before the list', 'msls' ), array( $this, 'before_output' ), __CLASS__, 'section' );
        add_settings_field( 'after_output', __( 'Text/HTML after the list', 'msls' ), array( $this, 'after_output' ), __CLASS__, 'section' );
        add_settings_field( 'before_item', __( 'Text/HTML before each item', 'msls' ), array( $this, 'before_item' ), __CLASS__, 'section' );
        add_settings_field( 'after_item', __( 'Text/HTML after each item', 'msls' ), array( $this, 'after_item' ), __CLASS__, 'section' );
        add_settings_field( 'content_filter', __( 'Add hint for available translations', 'msls' ), array( $this, 'content_filter' ), __CLASS__, 'section' );
        add_settings_field( 'content_priority', __( 'Hint priority', 'msls' ), array( $this, 'content_priority' ), __CLASS__, 'section' );
        add_settings_field( 'image_url', __( 'Custom URL for flag-images', 'msls' ), array( $this, 'image_url' ), __CLASS__, 'section' );
    }

    /**
     * Section is just a placeholder
     */
    public function section() {}

    /**
     * Shows the select-form-field 'display'
     */
    public function display() {
        echo $this->render_select(
            'display',
            MslsLink::get_types_description(),
            $this->options->display
        );
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
        $selected = (
            !empty( $this->options->content_priority ) ? 
            $this->options->content_priority :
            10
        );
        echo $this->render_select( 'content_priority', $arr, $selected );
    }

    /**
     * Alternative image-url
     * 
     * @todo This is a value of a directory-url which should be more clear
     */
    public function image_url() {
        echo $this->render_input( 'image_url' );
    }

    /**
     * Render form-element (checkbox)
     * 
     * @param string $key Name and ID of the form-element
     * @return string HTML-Code of the checkbox
     */
    public function render_checkbox( $key ) {
        return sprintf(
            '<input type="checkbox" id="%1$s" name="msls[%1$s]" value="1"%2$s/>',
            $key,
            ( $this->options->$key == 1 ? ' checked="checked"' : '' )
        );

    }

    /**
     * Render form-element (text-input)
     *
     * @param string $key Name and ID of the form-element
     * @param string $size Size-attribute of the input-field
     * @return string HTML-code of the input-field
     */
    public function render_input( $key, $size = '30' ) {
        return sprintf(
            '<input id="%1$s" name="msls[%1$s]" value="%2$s" size="%3$s"/>',
            $key,
            esc_attr( $this->options->$key ),
            $size
        );
    }

    /**
     * Render form-element (select)
     *
     * @param string $key Name and ID of the form-element
     * @param array $arr Options as associative array
     * @param string $selected Values which should be selected
     * @return string HTML-code of the select-input
     */
    public function render_select( $key, array $arr, $selected ) {
        $options = array();
        foreach ( $arr as $value => $description ) {
            $options[] = sprintf(
                '<option value="%s"%s>%s</option>',
                $value,
                ( $value == $selected ? ' selected="selected"' : '' ), 
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
     * 
     * @param array $input Values of the submitted form
     * @return array Validated input 
     */ 
    public function validate( array $input ) {
        if ( !is_numeric( $input['display'] ) )
            $input['display'] = 0;
        $input['image_url'] = esc_url( rtrim( $input['image_url'], '/' ) );
        return $input;
    }

}
