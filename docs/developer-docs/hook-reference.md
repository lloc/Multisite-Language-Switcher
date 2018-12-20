# Hook reference
The **Multisite Language Switcher** provides some filters and actions, which are very useful for programmers who want to interact with the plugin from their functions and classes.

**And again: Always backup your files and database prior to work on your site, better safe than sorry.**

> *Read first the [introduction](http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters) provided by the WordPress community if you don't know what hooks are.*

## msls\_admin\_icon\_get\_edit\_new ##

If you want to change the link of the admin icon in case there is no object connected then this filter is for you. The default is that the link points to the 'create new page' of this object type.

Example:

    /**
     * @param string $path
     * @return string
     */
    function my_msls_admin_icon_get_edit_new( $path ) {
        return add_query_arg( array( 'abc' => 'xyz' ), $path );
    }
    add_filter( 'msls_admin_icon_get_edit_new', 'my_msls_admin_icon_get_edit_new' );

*This would add the parameter 'abc' with the value 'xyz' to the url path.*

## msls\_admin\_advanced\_section

Add input fields to the advanced section of the plugin settings ([code example](#msls-admin-main-section)).

## msls\_admin\_language\_section

Add input fields to the language section of the plugin settings ([code example](#msls-admin-main-section)). 

## msls\_admin\_main\_section

If you want to add custom input fields to the existing sections in the settings of the plugin then you can use one of these actions.

    /**
     * @param string $page
     * @param string $section
     */
    function my_msls_admin_main_section( $page, $section ) {
        add_settings_field( 'custom_field', __( 'Custom Field' ), 'custom_field', $page, $section );
    }
    add_action( 'msls_admin_main_section', 'my_msls_admin_main_section', 10, 2 );
    
    function custom_field() {
        printf( 
            '<input id="custom_field" name="msls[custom_field]" value="%s" size="30"/>',
            esc_attr( MslsOptions::instance()->custom_field )
        );
    }

*This would add an input field 'custom field' to the main-section of the settings-page.*

## msls\_admin\_register

If you want to add a completely customized settings section to the configuration-pages of the Multisite Language Switcher then you can use this action.

    /**
     * @param string $page
     */
    function my_msls_admin_register( $page ) {
        add_settings_section( 'custom_section', __( 'Custom Settings' ), 'custom_section', $page );
        add_settings_field( 'custom_field', __( 'Custom Field' ), 'custom_field', $page, 'custom_section' );
    }
    add_action( 'msls_admin_register', 'my_msls_admin_register' );
    
    function custom_section() { }
    
    function custom_field() {
        printf(
            '<input id="custom_field" name="msls[custom_field]" value="%s" size="30"/>',
            esc_attr( MslsOptions::instance()->custom_field )
        );
    }

*This would add a section with the id 'custom section' and an input field 'custom field' to the settings-page of the plugin.*

## msls\_admin\_validate

You can use this filter if you want to add some custom validation for an input field in the configuration page of the plugin.

    /**
     * @param array $arr
     * @return array
     */
    function my_msls_admin_validate( $arr ) {
        $arr['abc'] = ( isset( $arr['abc'] ) ? $arr['abc'] : 'xyz' ); 
    }
    add_filter( 'msls_admin_validate', 'my_msls_admin_validate' );

*This can be useful if you have added your own input fields to this page using an action hook like 'msls\_admin\_register'.*

## msls\_blog\_collection\_construct ##

You can easily manipulate the blog collection.

    /**
     * @param array $blogs
     * @return array
     */
    function my_msls_blog_collection_construct( $blogs ) {
        if ( isset( $blogs[1] ) ) {
            unset( $blogs[1] );
        }
        return $blogs;
    }
    add_filter( 'msls_blog_collection_construct', 'my_msls_blog_collection_construct' );

*This would exclude the blog with the ID 1 from the collection.*

## msls\_blog\_collection\_description ##

The description string of every blog is not fixed. You can override it.

    /**
     * @param int $blog_id
     * @param string|bool $description
     * @return string|bool
     */
    function my_msls_blog_collection_description( $blog_id, $description = false ) {
        $arr = array( 1 => 'abc', 2 => 'xyz' );
        if ( isset( $arr[ $blog_id ] ) ) {
            $description = $arr[ $blog_id ];
        }
        return $description;
    }
    add_filter( 'msls_blog_collection_description', 'my_msls_blog_collection_description', 10, 2 );

*This would give the blog with the ID 1 the description 'abc' and the blog with the ID 2 the name 'xyz.*

## msls\_filter\_string ##

You can override the string for the output of the translation hint. *$output* contains the original string and *$links* is an array of generated links to the available translations.

    /**
     * @param string $output
     * @param array $links
     * @return string
     */
    function my_msls_filter_string( $output, $links ) {
        if ( empty( $links ) ) {
            $output = '';
        }
        else {
            $output = sprintf(
                '<p>%s</p><ul><li>%s</li></ul>',
                __( 'Available Translations' ),
                implode( '</li><li>', $links )
            );
        }
        return $output;
    }
    add_filter( 'msls_filter_string', 'my_msls_filter_string', 10, 2 );

*You can use the following code if you want to remove this filter completely:*

    remove_action( 'the_content', 'msls_content_filter' );

## msls\_head\_hreflang ##

The plugin creates automatically [hreflang annotations](https://support.google.com/webmasters/answer/189077?hl=en) in the head of the generated HTML using the code of the language only (eg. en, de, fr). You can easily override this value:

    /**
     * @param string $language
     * @return string
     */
    function my_msls_head_hreflang( $language ) {
        if ( 'en' == $language ) {
            $language = 'en-gb';
        }
        return $language;
    }
    add_filter( 'msls_head_hreflang', 'my_msls_head_hreflang' );

*You can use the following code if you want to remove these tags completely:*

    remove_action( 'wp_head', 'msls_head' );

## msls\_link\_create ##

There is also a way to manipulate the inner HTML in the generated links. Just create your own MslsLink class.

    /**
     * @param int $display
     * @return MslsLink
     */
    function my_msls_link_create( $display ) {
        class MyMslsLink extends MslsLink {
            protected $format_string = '<img src="{src}" alt="{alt}"/> <span>{txt}</span>';
        }
        return new MyMslsLink;
    }
    add_filter( 'msls_link_create', 'my_msls_link_create' );

*This adds label tags to the descriptional text after the flag icon.*

## msls\_main\_save ##

With this action you can call a completely customized save-routine.

    /**
     * @param int $object_id
     * @param string $class
     */
    function my_msls_main_save( $object_id, $class ) {
        // Your code here
    }
    add_action( 'msls_main_save', 'my_msls_main_save' );

*Use this action only if you exactly know what you do!*

## msls\_meta\_box\_render\_input\_button ##

You can change or hide the button of the meta box in the edit-screen with this filter.

    /**
     * @param string $input_button
     */
    function my_hide_input_button( $input_button ) {
        return '<!-- ' . $input_button . ' -->';
    }
    add_filter( 'msls_meta_box_render_input_button', 'my_hide_input_button' );

*This wraps the code of the submit-button into HTML comments.*

## msls\_meta\_box\_suggest\_args

    /**
     * @param array $args
     * @return array
     */
    function my_msls_meta_box_suggest_args( $args ) {
        $args['posts_per_page'] = 5;
        return $args;
    }
    add_filter( 'msls_meta_box_suggest_args', 'my_msls_meta_box_suggest_args' );

Maybe you will find it useful to be able to override the [WP_Query](http://codex.wordpress.org/Class_Reference/WP_Query) *$args* for the auto-complete search-field in the meta box which you can see in the edit-screen of the various post-types or taxonomies of your WordPress site.

*This would limit the output of the results to a maximum of 5 posts.*

## msls\_post\_tag\_suggest\_args ##

Read on here: [msls\_meta\_box\_suggest\_args](#msls-meta-box-suggest-args)

## msls\_meta\_box\_render\_select\_hierarchical ##

This is only valid for hierarchical post types that use the HTML select (and _not_ the new autocomplete inputs)in the meta box.

    /**
     * @param array $args
     * @return array
     */
    function my_msls_meta_box_render_select_hierarchical( $args ) {
        $args['post_status'] = array( 'publish', 'draft', 'future', 'pending' );
        return $args;
    }
    add_filter( 'msls_meta_box_render_select_hierarchical', 'my_msls_meta_box_render_select_hierarchical' );

This adds various post_stati to the array _$args_ for _wp\_dropdown\_pages even if this function does not expect this. But inside the function is a call to _get\_pages_ that can use this param.

## msls\_meta\_box\_suggest\_post

You can even manipulate the [WP_Post](http://codex.wordpress.org/Class_Reference/WP_Post)- or Term-objects in the result-set created in 'msls\_meta\_box\_suggest\_args' or 'msls\_post\_tag\_suggest\_args'. 

    /**
     * @param \WP_Post $post
     * @return \WP_Post
     */
    function my_msls_meta_box_suggest_post( $post ) {
        $post->post_title .= ' (' . $post->ID . ')';
        return $post;
    }
    add_filter( 'msls_meta_box_suggest_post', 'my_msls_meta_box_suggest_post' );

*This would add the post_id to the title of the posts in the autocomplete-field of the meta box.*

## msls\_post\_tag\_suggest\_term

Read on here: [msls\_meta\_box\_suggest\_post](#msls-meta-box-suggest-post)

## msls\_options\_get\_available\_languages

You can create a custom function to filter the available languages used in the language section of the plugin-settings.

    /**
     * @param array $languages
     * @return string
     */
    function my_msls_options_get_available_languages( languages ) {
        if ( ! isset( $languages['en_GB'] ) ) {
            $languages['en_GB'] = 'British English';
        }
        return $languages;
    }
    add_filter( 'msls_options_get_available_languages', 'my_msls_options_get_available_languages' );

*Even if it is still not fully tested, it seems to be a smart way to add a language without the language files installed. In this case it would solve the issue with the American flag and the Union Jack.*

## msls\_options\_get\_flag\_url

You can set the path to the flag-icons in the admin settings of the plugin but you can also override the path with a filter.

    /**
     * @param string $url
     * @return string
     */
    function my_msls_options_get_flag_url( $url ) {
        return get_stylesheet_directory_uri() . '/images/';
    }
    add_filter( 'msls_options_get_flag_url', 'my_msls_options_get_flag_url' );

*This 'sets' the path to the flag icons to the directory 'images' in the active theme.*

## msls\_options\_get\_permalink

I decided to add a filter to the implementation of get_permalink in the plugin so that I can offer a workaround for the issues with the possibility to localize the slugs of custom post types.

    /**
     * @param string $postlink
     * @param string $language
     * @return string
     */
    function my_msls_options_get_permalink( $url, $language ) {
        if ( 'de_DE' == $language ) {
            $url = str_replace( '/products/', '/produkte/', $url );
        }
        return $url;
    }
    add_filter( 'msls_options_get_permalink', 'my_msls_options_get_permalink', 10, 2 );

*This replaces the 'products'-part in the URL with 'produkte' if $language is 'de_DE'.*

## msls\_output\_get

You can use this filter if you want to change the format of the generated links to the translations in your blog.

    /**
     * @param string $url
     * @param lloc\Msls\MslsLink $link
     * @param bool current
     * @return string
     */
    function my_msls_output_get( $url, $link, $current ) {
        return sprintf(
            '<a href="%s" title="%s"%s>%s</a>',
            $url,
            $link->txt,
            ( $current ? ' class="current"' : '' ),
            $link
        );
    }
    add_filter( 'msls_output_get', 'my_msls_output_get', 10, 3 );

*This would transform the absolute URL in a relative one and would add the css-class 'current' to the link of the current blog.*

## msls\_output\_get\_tags

You can configure the output-tags in the admin settings of the plugin but you can also override these with a filter.

    /**
     * @param array $tags
     * @return array
     */
    function my_msls_output_get_tags( $tags ) {
        return array(
            'before_item'   => '<li>',
            'after_item'    => '</li>',
            'before_output' => '<ul>',
            'after_output'  => '</ul>',
        );
    }
    add_filter( 'msls_output_get_tags', 'my_msls_output_get_tags' );

*This would override completely the configuration without looking for existing values.*

## msls\_widget\_alternative\_content

The widget will output "No available translations found" if you set "Show only links with a translation" in the plugin options and if there is no translation available. You can override the output-string:

    /**
     * @param string $text
     * @return array
     */
    function my_msls_widget_alternative_content( $text ) {       
        return '';
    }
    add_filter( 'msls_widget_alternative_content', 'my_msls_widget_alternative_content' );

*This would be helpful if you want to show an empty string.*