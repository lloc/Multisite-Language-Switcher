# Snippets & Examples

A collection of small, focused recipes that show how to integrate the
Multisite Language Switcher into a theme or another plugin. The first three
mirror the canonical examples published on
[msls.co](https://msls.co/developer-docs/snippets-examples/); the rest are
short worked examples for the public API functions in
[`docs/api.md`](api.md) and the hooks in [`docs/hooks.md`](hooks.md).

Each snippet is intentionally minimal. Treat it as a starting point: copy it
into your `functions.php` or a small mu-plugin, then adapt it to your theme
locations, post types, and locales.

## Get the current language

In most cases you should use the WordPress function `get_locale()` when you
need the language of the current blog. MSLS exposes a near-equivalent through
its blog collection — with the bonus of falling back to `en_US` when the
value is empty — and gives you the two-letter alpha-2 code as a separate
accessor.

```php
$blog     = msls_blog_collection()->get_current_blog();
$language = $blog->get_language(); // en_US
$alpha2   = $blog->get_alpha2();   // en
```

## Manipulate the nav menu

The `Frontend\Output` class is the right entry point when you want to inject
the language switcher into a navigation menu. The example below renders the
links as flag-only items and appends them to the primary menu only.

It's safer to gate on the existence of `msls_output()` than to reference the
class directly — that way the theme degrades gracefully when MSLS is
deactivated.

```php
function my_custom_menu_item( $items, $args ) {
    if ( function_exists( 'msls_output' ) && 'primary' === $args->theme_location ) {
        $arr = msls_output()->get( 2 );
        if ( ! empty( $arr ) ) {
            $items .= '<li>' . implode( '</li><li>', $arr ) . '</li>';
        }
    }

    return $items;
}
add_filter( 'wp_nav_menu_items', 'my_custom_menu_item', 10, 2 );
```

The integer passed to `get()` selects the link variant:

```php
/* Link – flag image plus text */
$arr = $obj->get( 0 );

/* TextOnly – just the language text */
$arr = $obj->get( 1 );

/* ImageOnly – just the flag image */
$arr = $obj->get( 2 );

/* TextImage – text label plus flag image */
$arr = $obj->get( 3 );
```

## Iterate the blog collection

When you need to do something for every blog the plugin tracks — emit
alternate links, build a custom menu, populate a sitemap — iterate the
collection directly:

```php
function my_print_something() {
    foreach ( msls_blog_collection()->get() as $blog ) {
        printf(
            '<link rel="alternate" hreflang="%1$s" href="http://%1$s.example.com/" />',
            $blog->get_language()
        );
    }
}
add_action( 'wp_head', 'my_print_something' );
```

Note that `msls_blog_collection()->get()` returns every blog except the
current one. Use `->get_objects()` instead if you need the full list.

## Render the switcher in a template

The single-line drop-in for theme files. Wrap it in a `function_exists()`
guard so the template still renders if the plugin is disabled.

```php
if ( function_exists( 'msls_the_switcher' ) ) {
    msls_the_switcher();
}
```

To capture the markup as a string — for example to embed it in another
component — use `msls_get_switcher()` and forward optional tag overrides:

```php
$markup = msls_get_switcher(
    array(
        'before_output' => '<ul class="lang">',
        'after_output'  => '</ul>',
        'before_item'   => '<li>',
        'after_item'    => '</li>',
    )
);
```

## Link to a single translation

Use `msls_get_permalink()` when you want to build your own switcher markup or
a single direct link to one specific language. The optional second argument
is returned when no translation is available, which makes the call safe to
embed inline:

```php
$de_url = msls_get_permalink( 'de_DE', home_url( '/' ) );
echo '<a href="' . esc_url( $de_url ) . '">Deutsche Version</a>';
```

## Customize the hreflang value

`msls_head_hreflang` filters the value of the `hreflang` attribute generated
in `<head>`. Use it to enforce a regional locale (for example `en-GB`
instead of plain `en`) for SEO purposes:

```php
add_filter( 'msls_head_hreflang', function ( $language ) {
    return 'en_US' === $language ? 'en-US' : $language;
} );
```

## Restrict supported post types

By default MSLS treats every public post type as translatable. The
`msls_supported_post_types` filter lets you opt specific post types out — for
example a `feedback` CPT that you keep mono-lingual:

```php
add_filter( 'msls_supported_post_types', function ( array $types ): array {
    return array_values( array_diff( $types, array( 'feedback' ) ) );
} );
```

The corresponding `msls_supported_taxonomies` filter does the same for
taxonomies.

## Override the per-language link markup

`msls_output_get` runs for every rendered language item just before it joins
the output array. The example adds an `aria-current="true"` attribute when
the item points at the current blog and otherwise leaves the markup alone:

```php
add_filter( 'msls_output_get', function ( string $url, $link, bool $is_current_blog ): string {
    $current_attr = $is_current_blog ? ' aria-current="true"' : '';

    return sprintf( '<a href="%s" hreflang="%s"%s>%s</a>',
        esc_url( $url ),
        esc_attr( $link->get_language() ),
        $current_attr,
        esc_html( (string) $link )
    );
}, 10, 3 );
```

## Set the status of REST-created translations

The Quick Create REST endpoint inserts new translations as drafts by
default. Hook `msls_quick_create_post_data` to inherit the source post's
status instead:

```php
add_filter( 'msls_quick_create_post_data', function ( array $post_data, \WP_Post $source ): array {
    $post_data['post_status'] = $source->post_status;

    return $post_data;
}, 10, 2 );
```

Remember that any custom callback runs alongside the built-in
`lloc\Msls\RestApi\RestApi::prefix_source_language()` filter, which prepends
the source locale to the new post's title. Remove it with `remove_filter()` if
that behavior is unwanted in your workflow:

```php
remove_filter(
    'msls_quick_create_post_data',
    array( \lloc\Msls\RestApi\RestApi::class, 'prefix_source_language' ),
    10
);
```

## See also

- [Public API Functions](api.md) — the full reference for every `msls_*`
  helper.
- [Hooks Reference](hooks.md) — the full list of actions and filters the
  plugin emits.
