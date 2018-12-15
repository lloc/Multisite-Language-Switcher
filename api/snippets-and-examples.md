# Snippets & examples

Most people are likely to use the widget provided by the **Multisite Language Switcher**. But the dynamic sidebars are not always the best place for the output of the plugin.

**Please keep in mind: Always backup your files and database prior to work on your site, better safe than sorry.**

> _If you look for explanations of the basic functions, classes and methods of the plugin, then head over_ [_to this page_](http://msls.co/functions-classes-and-methods)_._

### Get the current language <a id="get-the-current-language"></a>

You should use the function **get\_locale\(\)** provided by WordPress if you want to know the language of the current blog.

The Multisite Language Switcher has also a similar functionality that sets the language to ‘en\_US’ in case it is empty.

```text
use lloc\Msls\MslsBlogCollection;

$blog     = MslsBlogCollection::instance()->get_current_blog();
$language = $blog->get_language(); // en_US
$alpha2   = $blog->get_alpha2(); // en
```

### Manipulate the Navigation Menu <a id="manipulate-the-navigation-menu"></a>

The class _MslsOutput_ comes in handy when you’d like to manipulate the items of a Navigation Menu. First you should check if plugin is active \(just check for the existence of function ‘the\_msls’\) and if it is the _primary_ menu in your theme \(the name can vary\). Then you can create the object and request the array of links \(in my example just linked flags\) to the translations. After that you can create the output, add it to _$items_ and return it.

Here comes a simplified version of the add-on [MslsMenu](https://github.com/lloc/MslsMenu):

```text
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
```

You could pass - of course - other values different from _2_. Here is a list with all possibilities:

```text
/* MslsLink - Image + text */
$arr = $obj->get( 0 );
 
/* MslsLinkTextOnly - Just text	*/
$arr = $obj->get( 1 );
 
/* MslsLinkImageOnly - Just image */
$arr = $obj->get( 2 );
 
/* MslsLinkTextImage - Text + image */
$arr = $obj->get( 3 );
```

### Use the blog collection in your functions \(and filters\) <a id="use-the-blog-collection-in-your-functions-and-filters"></a>

If you want to use the collection of blogs - which created the plugin - in your functions \(and filters\) you could write code like this:

```text
use lloc\Msls\MslsBlogCollection;

function my_print_something() {
    foreach ( MslsBlogCollection::instance()->get() as $blog ) {
        printf(
            '<link rel="alternate" hreflang="%1$s" href="http://%1$s.example.com/" />',
            $blog->get_language()
        );
    }
}
add_action( 'wp_head', 'my_print_something' );
```

The above example prints the link references to your blogs in all html headers of the output. This is just a simplified version of what the plugin already does.

