# Snippets & examples

Most people are likely to use the widget provided by the Multisite Language Switcher. But the dynamic sidebars are not always the best place for the output of the plugin.

> Please keep in mind: Always backup your files and database prior to work on your site, better safe than sorry.

*If you look for explanations of the basic functions, classes and methods of the plugin, then head over to [this page](/user-docs/).*

## Get the current language

You should use the function **get_locale()** provided by WordPress if you want to know the language of the current blog.

The Multisite Language Switcher has also a similar functionality that sets the language to ‘en_US’ in case it is empty.

    use lloc\Msls\MslsBlogCollection;
    ​
    $blog     = MslsBlogCollection::instance()->get_current_blog();
    $language = $blog->get_language(); // en_US
    $alpha2   = $blog->get_alpha2(); // en

## Manipulate the Nav Menu

The class MslsOutput comes in handy when you’d like to manipulate the items of a Navigation Menu. First you should check if plugin is active (just check for the existence of function ‘the_msls’) and if it is the primary menu in your theme (the name can vary). Then you can create the object and request the array of links (in my example just linked flags) to the translations. After that you can create the output, add it to $items and return it.

Here comes a simplified version of the add-on MslsMenu:

    function my_custom_menu_item( $items, $args ) {
        if ( function_exists ( 'the_msls' ) && 'primary' == $args->theme_location ) {
            $obj = new lloc\Msls\MslsOutput::init();
            $arr = $obj->get( 2 );
            if ( !empty( $arr ) ) {
                $items .= '<li>' . implode( '</li><li>', $arr ) . '</li>';
            }
        }
        return $items;
    }
    add_filter( 'wp_nav_menu_items', 'my_custom_menu_item', 10, 2 );

You could pass - of course - other values different from 2. Here is a list with all possibilities:

    /* MslsLink - Image + text */
    $arr = $obj->get( 0 );

    /* MslsLinkTextOnly - Just text	*/
    $arr = $obj->get( 1 );

    /* MslsLinkImageOnly - Just image */
    $arr = $obj->get( 2 );

    /* MslsLinkTextImage - Text + image */
    $arr = $obj->get( 3 );

## The blog collection

If you want to use the collection of blogs - which created the plugin - in your functions (and filters) you could write code like this:

    use lloc\Msls\MslsBlogCollection;
    ​
    function my_print_something() {
        foreach ( MslsBlogCollection::instance()->get() as $blog ) {
            printf(
                '<link rel="alternate" hreflang="%1$s" href="http://%1$s.example.com/" />',
                $blog->get_language()
            );
        }
    }
    add_action( 'wp_head', 'my_print_something' );`

The above example prints the link references to your blogs in all html headers of the output. This is just a simplified version of what the plugin already does.
