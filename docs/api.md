# Public API Functions

Multisite Language Switcher ships with a small set of global helper functions
that act as the plugin's public API. They live in `includes/api.php`, which
`MultisiteLanguageSwitcher.php` requires the moment the plugin file is
included — before `plugins_loaded` fires. They are therefore available to
add-ons and themes regardless of the order in which WordPress loads the
plugins.

Use these functions in templates, shortcodes, blocks, or other plugins
whenever you need to render the switcher, resolve a translation URL, or
reach into MSLS without instantiating its internal classes directly. They
also wrap MSLS's registry/singleton plumbing, which means calling them
repeatedly is cheap and safe.

A `function_exists()` guard is recommended before any of these calls in theme
templates, so your theme degrades gracefully when MSLS is deactivated:

```php
if ( function_exists( 'msls_the_switcher' ) ) {
    msls_the_switcher();
}
```

## Rendering the switcher

### msls_the_switcher

Prints the language switcher in your template. The optional array argument is
forwarded to the underlying `Output` object as tag overrides (`before_output`,
`after_output`, `before_item`, `after_item`), which lets you wrap the
switcher in custom markup for a specific template without changing the
plugin's global settings.

### msls_get_switcher

Returns the language switcher as a string instead of printing it, so you can
embed it in other strings, return it from a shortcode, or pass it through
your own escaping/filtering. The optional argument behaves the same as for
`msls_the_switcher()`.

## Translation lookups

### msls_get_permalink

Returns the URL of the translation of the current post (or term, or archive)
in the language identified by the given locale. If no translation exists, the
optional `$preset` string is returned instead — typically an empty string or
a fallback URL. Useful when you need to render your own switcher markup or
build a single direct link to a specific language.

### msls_get_flag_url

Returns the URL of the flag icon for a given locale. The base directory can
be customized through the `msls_options_get_flag_url` filter and the file
name through `msls_options_get_flag_icon`, so this helper always points at
the configured icon source.

### msls_get_blog_description

Returns the textual description configured for the blog mapped to a given
locale (the same value used by MSLS settings as the language label). The
optional `$preset` is returned when no matching blog is registered.

### msls_blog

Resolves a locale to its `Blog` instance, or `null` when no blog with that
locale is part of the MSLS collection. Use it when you need direct access to
a single blog — for example to call `get_url()` for a custom switcher — and
want to handle the missing-blog case explicitly.

## Service accessors

### msls_blog_collection

Returns the `Blog\Collection` singleton, which represents every blog MSLS
tracks (their locales, descriptions, URLs, and the current blog). Reach for
it when you want to iterate over the full set of languages yourself.

### msls_options

Returns the global `Options` singleton — the merged plugin settings for the
current request. Use it to read configuration values such as the display
mode, image URL overrides, or whether MSLS should include the current blog
in the switcher.

### msls_output

Returns a fresh `Frontend\Output` instance built for the current request.
This is the same object `msls_the_switcher()` and `msls_get_switcher()` use
internally, so call it directly when you need to chain custom tag overrides
or inspect the resolved switcher state before rendering.

### msls_content_types

Returns the `ContentTypes\ContentTypes` instance, a context-aware factory
that exposes both supported post types and taxonomies. Use it when you need
to ask "is this current request a translatable content type?" without
worrying about whether you're on a post or a taxonomy screen.

### msls_post_type

Returns the `ContentTypes\PostType` singleton, which lists the post types
MSLS treats as translatable. Use it to check or iterate the supported post
types from outside the plugin.

### msls_taxonomy

Returns the `ContentTypes\Taxonomy` singleton, which lists the taxonomies
MSLS treats as translatable. Use it to check or iterate the supported
taxonomies from outside the plugin.

## Object-level translation accessors

### msls_get_post

Returns the `Options\Post\Post` instance for a specific post ID. The object
exposes the post's translation map — the IDs of the equivalent posts on
other blogs — and helpers such as `get_permalink()` for individual
languages.

### msls_get_tax

Returns the `Options\Tax\OptionsTaxInterface` instance for a specific term
ID. Depending on the current query (category, tag, or custom taxonomy) the
returned object is the most specific subclass available, so you can read
the term's translations without doing the context detection yourself.

### msls_get_query

Returns the `Options\Query\Query` instance for the current archive request
(day, month, year, author, or post type archive), or `null` when the current
request is not an archive. Use it to get translation URLs for date- or
archive-based pages, which `msls_get_permalink()` already wraps but which
you might need to query in more detail.

## Internal helpers

### msls_return_void

A trivial no-op function. It exists so that callers (typically WordPress
action registrations or fluent setups) can pass a callable that "does
nothing" without inventing a closure. There's no end-user use case here —
mentioned only for completeness.

## Deprecated functions

The following pre-2.10.1 names live in `includes/deprecated.php`. Each one
still works but emits a `_deprecated_function()` notice and simply forwards
to its modern `msls_*` replacement. Update calls in your code at your
earliest convenience.

### get_the_msls

Deprecated since 2.10.1 — use `msls_get_switcher()`.

### the_msls

Deprecated since 2.10.1 — use `msls_the_switcher()`.

### get_msls_flag_url

Deprecated since 2.10.1 — use `msls_get_flag_url()`.

### get_msls_blog_description

Deprecated since 2.10.1 — use `msls_get_blog_description()`.

### get_msls_permalink

Deprecated since 2.10.1 — use `msls_get_permalink()`.
