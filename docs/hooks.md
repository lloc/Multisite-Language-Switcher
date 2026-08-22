# Hooks Reference

Multisite Language Switcher exposes a number of WordPress actions and filters
that let developers customize the plugin's behavior without modifying its
source. Almost every hook name is prefixed with `msls_`, which makes them easy
to find in the codebase with a quick `grep -rn "msls_" includes/`.

This reference lists every extension point the plugin emits, grouped by
subsystem. For each hook you'll find a short heading with the literal hook
name and a paragraph explaining what it does and a typical use case. To learn
the exact arguments a hook receives, grep for the hook name in `includes/` —
the `apply_filters()` / `do_action()` call site is the source of truth.

## What add-ons can rely on

Everything an add-on needs is in place from the moment
`MultisiteLanguageSwitcher.php` is included — before `plugins_loaded` fires, and
therefore regardless of the order in which WordPress happens to load the two
plugins:

* the `msls_*()` functions of `includes/api.php` and the deprecated wrappers of
  `includes/deprecated.php`
* every class under `lloc\Msls\`, through Composer's PSR-4 autoloader
* every pre-3.0 flat class name (`lloc\Msls\MslsOptions`,
  `lloc\Msls\MslsAdmin`, `lloc\Msls\MslsLink`, `lloc\Msls\MslsOutput`, …).
  The 3.0 restructuring moved these into sub-namespaces; `lloc\Msls\Compat\Aliases`
  registers them as `class_alias()` entries, so `class_exists( 'lloc\Msls\MslsOptions' )`
  keeps working and the old names are equally valid in type declarations. Write
  new code against the namespaced names — the aliases exist for third-party
  consumers.

The hooks below, by contrast, fire during `plugins_loaded` and later.

## Frontend output and links

### msls_get_output

Filter that lets you replace the `Output` instance used by `msls_the_switcher()`
and `msls_get_switcher()`. By default it returns `null` and the global helper
falls back to `Output::create()`. Return your own object (implementing the
same `set_tags()` / `__toString()` contract) to substitute a fully custom
switcher renderer for the entire site or per template.

### msls_output_get

Filter that builds the markup for every individual language item before it
joins the output array. It receives the target URL (not the finished anchor),
the `LinkInterface` object, and whether the item points at the current blog —
so the return value has to be the complete HTML for that item. When no
callback is attached, MSLS renders its own default anchor instead. Use it to
wrap, decorate, or replace the per-language link — for example to add a CSS
class, swap in a button element, or append a flag image only on the current
language.

### msls_output_get_tags

Filter on the wrapper tags (`before_output`, `after_output`, `before_item`,
`after_item`) that surround the rendered switcher. These tags come from the
plugin settings, but the filter lets you override them programmatically, for
example to force a different list markup in certain templates.

### msls_output_no_translation_found

Filter on the empty string that `Output::__toString()` returns when there is
nothing to render (no translation found and the "show current language" option
is off). Use it to surface a notice such as "This article is not yet available
in your language" instead of an empty switcher.

### msls_output_get_alternate_links

Filter on the URL of each alternate-language entry that the plugin emits as
`<link rel="alternate" hreflang="…">` tags in `<head>`. Return an empty string
to drop a specific language from the alternate list, or rewrite the URL to
match a canonical form your SEO setup requires.

### msls_output_get_alternate_links_default

Filter on the `x-default` alternate `<link>` tag that is returned when only one
alternate URL is available. Override this when you want to suppress, replace,
or rewrite that default entry.

### msls_output_get_alternate_links_arr

Filter on the full array of `<link rel="alternate">` strings before they are
joined and printed in `<head>`. Use it to reorder languages, drop entries
unconditionally, or inject additional `<link>` tags.

### msls_head_hreflang

Filter on the `hreflang` attribute value (for example `en` or `en-US`) for a
given language. By default the plugin derives the value from the blog locale
and only widens it to a regional form when several blogs share the same base
language. Override the hook to enforce your own locale-to-hreflang policy.

### msls_filter_string

Filter on the "This post is also available in …" hint that the plugin can
inject above or below the post content. Return your own translated/branded
template string — the existing list of language links is passed alongside so
your replacement can still reference them.

### msls_widget_title

Filter on the default title ("Multisite Language Switcher") displayed for the
classic widget. Useful when you want every widget instance in your network to
inherit the same custom name without editing each widget area.

### msls_widget_alternative_content

Filter on the fallback text shown inside the widget when no translations are
available for the current page. The default is "No available translations
found"; override it to localize differently or render a custom call-to-action.

## Link rendering

### msls_link_create

Filter that fires inside the `Link` factory when a switcher item is being
materialized into one of the built-in link variants (text, image, image plus
text, etc.). Return a different object implementing `LinkInterface` to
substitute a fully custom link renderer — for example one that emits a button
component or an icon-only SVG.

## Permalinks and options

### msls_get_postlink

Filter on the resolved permalink for a translation. Triggered when the plugin
asks an `Options` object (post, term, or archive query) for the URL it should
point to in another language. Override it to rewrite translation URLs, for
example to route through a marketing redirector or to add tracking parameters.

### msls_options_get_permalink

Filter on the permalink produced for a specific language by the lower-level
`Options::get_permalink()` flow. This fires before `msls_get_postlink` and is
the right hook when you need to influence the URL for query archives or
taxonomy pages in particular, rather than every kind of object.

### msls_options_get_flag_url

Filter on the directory URL where flag-icon images are loaded from. Override
this to serve flags from your own CDN, theme, or asset pipeline instead of
the bundled `flag-icon/` directory.

### msls_options_get_flag_icon

Filter on the actual filename used for a flag icon for a given language. Use
it when you need to swap individual icons — for example to map a custom
locale to a non-standard flag file.

### msls_options_get_available_languages

Filter on the array of language codes and human-readable names that the plugin
considers selectable in its admin UI. Useful when you want to extend the
WordPress core language list with custom locales or hide locales that should
never appear in the per-blog dropdown.

## Blogs and collection

### msls_blog_collection_construct

Filter that fires while the blog `Collection` is being built. It receives the
array of blogs about to be tracked and lets you remove, reorder, or replace
entries before the collection is exposed to the rest of the plugin. Use this
to scope MSLS to a subset of network sites without changing per-blog settings.

### msls_blog_collection_description

Filter on the description string used for a blog inside the collection.
Override it to feed a custom label (for example pulled from blog meta) into
the language switcher instead of relying on the blog name.

### msls_blog_collection_get_blog

Filter on the `Blog` instance returned when callers look up a blog by its
language. Returning your own object — or returning a different blog than the
one MSLS would pick — lets you redirect language resolution, for example to
implement a fallback chain when an exact locale match is missing.

### msls_blog_collection_get_blog_id

Filter on the numeric blog ID returned when callers look up a blog by
language. Same intent as `msls_blog_collection_get_blog`, but for code paths
that only need the ID; override it to inject fallback IDs or override which
site serves a given locale.

### msls_blog_collection_get_objects

Filter on the full list of `Blog` objects in the collection. Use it to
globally reorder how blogs are surfaced (the order influences switcher output)
or to hide blogs from every consumer at once.

### msls_blog_collection_get

Filter on the list of blogs returned when the consumer asked for "all blogs
except the current one." This is the list typically iterated when rendering
the switcher on the frontend, so it's the right hook to filter for output
without affecting admin features that use the full collection.

### msls_get_users

Filter on the arguments passed to `get_users()` when MSLS builds its
"reference user" dropdown. Use it to restrict the candidate users — for
example by role, by capability, or by custom meta — so editors only see the
intended reference accounts.

### msls_blog_get_permalink

Filter on the permalink resolved from a `Blog` object for the current
translatable context (post, term, or archive). Use it to override the
translation URL on a per-blog basis after MSLS has decided which translated
object to link to.

## Content types

### msls_supported_post_types

Filter on the list of post types that MSLS treats as translatable. By default
this includes public post types; override it to opt in additional custom
post types, or to remove a public post type from MSLS's purview.

### msls_supported_taxonomies

Filter on the list of taxonomies that MSLS treats as translatable. Override it
to expose custom taxonomies to the translation UI or to remove taxonomies you
do not want MSLS to manage.

## Admin – settings page

### msls_admin_register

Action that fires after the plugin registers its built-in settings sections.
Use it as the entry point to add your own settings section to the MSLS admin
page via `add_settings_section()`.

The callback receives the settings page slug as its only argument. **Always
pass that argument through to `add_settings_section()` / `add_settings_field()`
instead of hard-coding a slug** — it is the only supported way to land on the
page MSLS actually renders:

```php
add_action(
	'msls_admin_register',
	function ( $page ) {
		add_settings_section( 'my_section', 'My Settings', 'my_render', $page );
	}
);
```

### msls_admin_{section}

Dynamic action that fires after the plugin registers the fields belonging to a
specific settings section. The `{section}` suffix matches the section ID, for
example `msls_admin_main_section`, `msls_admin_language_section`,
`msls_admin_advanced_section`, or `msls_admin_rewrites_section`. Use it to
add custom fields to one specific section without touching the others. Like
`msls_admin_register`, it hands you the page slug — as the first of its two
arguments, the second being the section ID.

### msls_admin_caps

Filter on the capability required to access the MSLS settings page. The
default is `manage_options`. Override it to delegate plugin configuration to
a different role or a custom capability.

### msls_max_reference_users_count

Filter on the upper bound (default 100) of users listed in the "reference
user" dropdown on the settings page. Increase the limit for networks with
many editors, or lower it to keep the dropdown light on big sites.

### msls_reference_users

Filter on the array of reference users — keyed by user ID and valued by
nicename — used to populate the "reference user" dropdown. Use it to
post-process the list after the user query has run, for example to relabel
entries or remove specific accounts.

### msls_admin_validate

Filter that runs over the form values submitted on the MSLS settings page
before they are saved. Use it to sanitize, normalize, or reject additional
fields you have added via `msls_admin_register` and `msls_admin_{section}`.

## Admin – post metabox

### msls_metabox_post_select_title

Filter on the title ("Multisite Language Switcher") of the main MSLS metabox
shown on post edit screens. Override it to localize or rebrand the section
heading.

### msls_metabox_post_import_title

Filter on the title ("Multisite Language Switcher – Import content") of the
content-import metabox shown alongside the main MSLS metabox. Override it to
rename that secondary box.

### msls_meta_box_suggest_args

Filter on the `WP_Query` arguments used by the metabox autocomplete to find
candidate posts on a target blog. Use it to add `meta_query` clauses, narrow
post statuses, or restrict the suggestion query in other ways.

### msls_meta_box_suggest_post

Filter on each `WP_Post` object before it becomes a suggestion entry. Use it
to enrich titles (for example with a status badge) or to skip individual
posts entirely by returning a value that won't be turned into a suggestion.

### msls_meta_box_suggest_results

Filter on the array of suggestions returned by the metabox autocomplete
endpoint before it is sent back as JSON. Use it to inject custom entries,
remove duplicates, or modify the label/value shape.

### msls_meta_box_render_select_hierarchical

Filter on the arguments passed to the hierarchical term selector used inside
the metabox. Override it to change ordering, depth, or other rendering
options for taxonomy pickers shown in the post translation UI.

## Admin – taxonomy screens

### msls_post_tag_add_input

Action that fires after the plugin renders its translation inputs on the
"add new term" screen of a taxonomy. Hook into it to append additional fields
or guidance text below the MSLS inputs.

### msls_post_tag_edit_input

Action that fires after the plugin renders its translation inputs on the
"edit term" screen of a taxonomy. The handler receives the `WP_Term` being
edited and the taxonomy slug, which is useful when your additions depend on
the specific term.

### msls_post_tag_classic_add_input

Same idea as `msls_post_tag_add_input`, but fires from the classic (no-
autocomplete) variant of the term translation UI. Use it when you want to
extend the legacy term-add screen, which renders a different markup wrapper.

### msls_post_tag_classic_edit_input

Same idea as `msls_post_tag_edit_input`, but for the classic variant of the
term edit screen.

### msls_post_tag_suggest_args

Filter on the `get_terms()` arguments used by the taxonomy autocomplete. Use
it to narrow the term query, for example to filter by language-specific
meta or to enforce a maximum result count.

### msls_post_tag_suggest_term

Filter on each `WP_Term` candidate before it becomes a term suggestion. Use
it to drop terms that should not be offered, or to relabel them on the fly.

### msls_post_tag_suggest_results

Filter on the array of term suggestions before the autocomplete endpoint
returns it as JSON. Use it to reorder, group, or augment the suggestions
shown in the term picker.

### msls_term_select_title

Filter on the heading ("Multisite Language Switcher") of the term-translation
section that the plugin adds to add/edit-term screens. Override it to rename
or localize that heading.

## Admin – misc

### msls_admin_icon_get_edit_new

Filter on the admin URL produced for the small MSLS edit/new icons shown in
list tables next to translatable rows. Override it to redirect the icon
target — for example to a custom translation workflow page instead of the
standard WordPress edit screen.

### msls_main_save

Action that, when registered, fully replaces the default save routine used by
the MSLS `Main` admin component. Register a callback to take over translation
persistence yourself: as soon as a handler is attached, MSLS skips its own
save logic and only fires the action with the object ID and the Options class
name. Use this to bridge MSLS into an external system of record.

### msls_input_select_name

Filter on the HTML `name` attribute generated for MSLS `<select>` form
elements (which default to the pattern `msls[<key>]`). Override it when you
need to integrate MSLS form fields into a different submit/parsing pipeline
that expects another name shape.

## REST API and Quick Create

### msls_quick_create_capability

Filter on the result of the Quick Create capability check. Alongside the
default decision you get the source post ID (`0` for list-style checks), the
source and target blog IDs, and a `$context` of either `read` (checking access
to the source post) or `create` (checking the right to insert on the target
blog). Use it to let a translator without an account on the source blog mirror
a post into the target blog, or to tighten the default checks.

### msls_quick_create_post_data

Filter on the post data array (title, content, status, post type, …) that
MSLS passes to `wp_insert_post()` when creating a translation on a target
blog via the REST Quick Create endpoint. Use it to prefix titles, set a
different post status, or inject taxonomy/meta defaults before insertion.
The built-in `Msls::prefix_source_language()` callback is registered on
this hook by default and can be removed with `remove_filter()`.

### msls_quick_create_after_insert

Action that fires after MSLS has inserted the new translation post on the
target blog (still inside the `switch_to_blog()` scope). Hook into it to copy
custom meta, run side effects (notifications, analytics), or seed additional
content for the freshly created translation.

### msls_quick_create_response

Filter on the REST response array (post ID, edit URL, title) returned by the
Quick Create endpoint. Use it to add fields your editorial UI expects, or to
strip information you do not want to leak to the client.

### msls_untranslated_posts

Filter on the list of untranslated posts returned by the REST endpoint that
powers the Quick Create "pick a post to translate" UI. Use it to reorder
items, hide drafts the editor should not see, or annotate each entry with
additional metadata.

### msls_quick_create_tax_input

Filter on the mapping of taxonomy slugs to translated term IDs prepared for
the new post on the target blog. Use it to inject default terms, drop
taxonomies you do not want carried over, or implement custom term-mapping
logic before terms are assigned.

## Content import

### msls_content_import_before_import

Action that fires immediately before the content-import pipeline runs. The
import coordinates (source blog, destination blog, post IDs, languages) are
passed in. Use it to log imports, prepare external resources, or short-
circuit by raising your own state.

### msls_content_import_after_import

Action that fires after the entire content-import pipeline has finished.
Alongside the coordinates you also get the `ImportLogger` and `Relations`
objects, which makes this the natural place to inspect what was imported,
post a notice, or trigger follow-up jobs.

### msls_content_import_data_before_import

Filter on the array of post fields that has been prepared for import, fired
just after the "before import" action and before any importer runs. Use it
to rewrite fields globally — for example to localize URLs in `post_content`
or set a translation-specific `post_status`.

### msls_content_import_data_after_import

Filter on the array of post fields after every importer has run. The hook
also receives the `ImportLogger` and `Relations` objects, so it is the
right place to do a last pass over the merged result — for example to undo
specific changes a particular importer made.

### msls_content_import_importers

Filter that can override the entire importers map for the current import.
Returning a non-null value short-circuits the default factory-based
construction and uses your map instead. Use this when you need a fully
custom import strategy for a specific scenario.

### msls_content_import_importers_map

Filter on the importers map after MSLS has built it from the factories.
Unlike `msls_content_import_importers`, this hook runs against the already-
constructed importers, so it is best for tweaking, adding, or removing
single importers without replacing the whole pipeline.

### msls_content_import_importers_factories_map

Filter on the map of importer factories before any importer is constructed.
Use it to inject your own factory (or remove an existing one) so the change
applies across every import, not just the current one.

### msls_content_import_{type}_importer

Dynamic filter, with `{type}` being the type of one of the five importer
factories: `post-fields`, `post-meta`, `terms`, `post-thumbnail`, or
`attachments`. Returning an `Importer` instance here forces the factory to use
that importer for the corresponding content type, bypassing the slug-based
selection.

### msls_content_import_{type}_importers_map

Dynamic filter, with `{type}` being one of the five factory types listed
above. It filters the map of available importer implementations
(slug ⇒ class) for that content type. Use it to register a new importer
flavor alongside the built-ins.

### msls_content_import_{type}_selected

Dynamic filter that picks which importer slug is "selected" for a given
factory — `{type}` is again one of the five factory types. The default is the
first entry of the factory's importers map. Use it to programmatically switch
between competing importer implementations, for example based on the post type
or destination blog.

### msls_content_import_log_writer

Filter on the log-writer object used to persist content-import logs. Return
your own implementation of `LogWriter` to route MSLS import logs into your
own log store instead of the bundled writer.

### msls_content_import_relation_local_to_source_create

Filter that lets you intercept the creation of a "local → source" relation
during import. Returning a non-null value tells MSLS that you have handled
relation creation yourself, so its built-in code path is skipped. Use this
to integrate translation relationships into a custom data model.

### msls_content_import_post_fields_whitelist

Filter on the list of post fields that the default duplicating importer
copies from the source post to the destination post. Use it to add fields
that should travel along (for example custom title/excerpt fields) or to
remove ones you never want copied across languages.

### msls_content_import_post_meta_blacklist

Filter on the list of post-meta keys that the default duplicating importer
refuses to copy. The default excludes keys like `_edit_last`, `_edit_lock`,
and `_thumbnail_id`; extend it to keep installation- or theme-specific keys
from being duplicated across translations.

### msls_content_import_term_meta_blacklist

Filter on the list of term-meta keys that the default shallow-duplicating
terms importer refuses to copy. Use it to keep specific term meta values
from leaking across translated taxonomies.
