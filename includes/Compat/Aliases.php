<?php declare( strict_types=1 );

namespace lloc\Msls\Compat;

use lloc\Msls\{Admin\Main, Data\Json, Data\LanguageArray, Plugin, Registry\GetSet, Request\Fields, RestApi\Request};
use lloc\Msls\Admin\{Admin,
	Bar,
	CustomColumn,
	CustomColumnTaxonomy,
	CustomFilter,
	Icon,
	IconTaxonomy,
	MetaBox,
	PostListActions,
	PostTag\PostTag};
use lloc\Msls\Admin\PostTag\{Classic as PostTagClassic};
use lloc\Msls\Admin\TranslationPicker\{Page as TranslationPickerPage, Table as TranslationPickerTable};
use lloc\Msls\Blog\{Blog, Collection as BlogCollection};
use lloc\Msls\Cli\Cli;
use lloc\Msls\ContentTypes\{ContentTypes, PostType as ContentPostType, Taxonomy};
use lloc\Msls\Db\Query\{AuthorPostsCounterQuery,
	BlogsInNetworkQuery,
	CleanupOptionsQuery,
	DatePostsCounterQuery,
	MonthPostsCounterQuery,
	TranslatedPostIdQuery,
	YearPostsCounterQuery};
use lloc\Msls\Db\SqlCacher;
use lloc\Msls\Frontend\{Block, ContentFilter, Output, ShortCode, Widget};
use lloc\Msls\Link\{ImageOnly, Link, LinkInterface, TextImage, TextOnly};
use lloc\Msls\Options\Options;
use lloc\Msls\Options\OptionsInterface;
use lloc\Msls\Options\Post\Post;
use lloc\Msls\Options\Query\{Author, Day, Month, PostType as QueryPostType, Query, Year};
use lloc\Msls\Options\Tax\{Category, OptionsTaxInterface, Tax, Term};
use lloc\Msls\Registry\{Instance as RegistryInstance, Registry};
use lloc\Msls\RestApi\RestApi;

/**
 * Backwards-compatibility aliases for the classes that were restructured from the flat
 * lloc\Msls\Msls* layout into per-concern sub-namespaces (Options\, Link\, Frontend\,
 * ContentTypes\) and for the interfaces that moved alongside their implementations
 * during 2.10.x.
 *
 * Third-party code that still references the old fully-qualified names continues to work
 * because self::register() turns every entry of self::MAP into a class_alias().
 *
 * The aliases have to be created eagerly, not from an autoloader: PHP resolves the class
 * named in a parameter, return or property type with ZEND_FETCH_CLASS_NO_AUTOLOAD, so a
 * lazily created alias never gets its chance and the call fatals with a TypeError. The
 * MslsMenu add-on declares `get_msls_output(): lloc\Msls\MslsOutput`, which is exactly
 * that case. The autoloader registered alongside is the safety net for the few names in
 * self::LAZY_ONLY.
 *
 * @package Msls
 */
final class Aliases {

	/**
	 * Maps every pre-3.0 fully-qualified name to the class or interface replacing it.
	 *
	 * @var array<string, class-string>
	 */
	public const MAP = array(
		'lloc\\Msls\\MslsOptions'                    => Options::class,
		'lloc\\Msls\\MslsOptionsPost'                => Post::class,
		'lloc\\Msls\\MslsOptionsQuery'               => Query::class,
		'lloc\\Msls\\MslsOptionsQueryAuthor'         => Author::class,
		'lloc\\Msls\\MslsOptionsQueryDay'            => Day::class,
		'lloc\\Msls\\MslsOptionsQueryMonth'          => Month::class,
		'lloc\\Msls\\MslsOptionsQueryPostType'       => QueryPostType::class,
		'lloc\\Msls\\MslsOptionsQueryYear'           => Year::class,

		'lloc\\Msls\\MslsOptionsTax'                 => Tax::class,
		'lloc\\Msls\\MslsOptionsTaxTerm'             => Term::class,
		'lloc\\Msls\\MslsOptionsTaxTermCategory'     => Category::class,

		'lloc\\Msls\\MslsLink'                       => Link::class,
		'lloc\\Msls\\MslsLinkImageOnly'              => ImageOnly::class,
		'lloc\\Msls\\MslsLinkTextImage'              => TextImage::class,
		'lloc\\Msls\\MslsLinkTextOnly'               => TextOnly::class,

		'lloc\\Msls\\LinkInterface'                  => LinkInterface::class,
		'lloc\\Msls\\OptionsInterface'               => OptionsInterface::class,
		'lloc\\Msls\\OptionsTaxInterface'            => OptionsTaxInterface::class,

		'lloc\\Msls\\MslsOutput'                     => Output::class,
		'lloc\\Msls\\MslsWidget'                     => Widget::class,
		'lloc\\Msls\\MslsBlock'                      => Block::class,
		'lloc\\Msls\\MslsShortCode'                  => ShortCode::class,
		'lloc\\Msls\\MslsContentFilter'              => ContentFilter::class,

		'lloc\\Msls\\MslsContentTypes'               => ContentTypes::class,
		'lloc\\Msls\\MslsPostType'                   => ContentPostType::class,
		'lloc\\Msls\\MslsTaxonomy'                   => Taxonomy::class,

		'lloc\\Msls\\MslsAdmin'                      => Admin::class,
		'lloc\\Msls\\MslsAdminBar'                   => Bar::class,
		'lloc\\Msls\\MslsAdminIcon'                  => Icon::class,
		'lloc\\Msls\\MslsAdminIconTaxonomy'          => IconTaxonomy::class,
		'lloc\\Msls\\MslsCustomColumn'               => CustomColumn::class,
		'lloc\\Msls\\MslsCustomColumnTaxonomy'       => CustomColumnTaxonomy::class,
		'lloc\\Msls\\MslsCustomFilter'               => CustomFilter::class,
		'lloc\\Msls\\MslsMetaBox'                    => MetaBox::class,
		'lloc\\Msls\\MslsPostListActions'            => PostListActions::class,

		'lloc\\Msls\\MslsTranslationPickerPage'      => TranslationPickerPage::class,
		'lloc\\Msls\\MslsTranslationPickerTable'     => TranslationPickerTable::class,

		'lloc\\Msls\\MslsBlog'                       => Blog::class,
		'lloc\\Msls\\MslsBlogCollection'             => BlogCollection::class,

		'lloc\\Msls\\MslsCli'                        => Cli::class,

		'lloc\\Msls\\MslsSqlCacher'                  => SqlCacher::class,

		'lloc\\Msls\\MslsRegistry'                   => Registry::class,
		'lloc\\Msls\\MslsRegistryInstance'           => RegistryInstance::class,

		'lloc\\Msls\\MslsPostTag'                    => PostTag::class,
		'lloc\\Msls\\MslsPostTagClassic'             => PostTagClassic::class,

		'lloc\\Msls\\MslsRestApi'                    => RestApi::class,

		'lloc\\Msls\\Query\\AuthorPostsCounterQuery' => AuthorPostsCounterQuery::class,
		'lloc\\Msls\\Query\\BlogsInNetworkQuery'     => BlogsInNetworkQuery::class,
		'lloc\\Msls\\Query\\CleanupOptionsQuery'     => CleanupOptionsQuery::class,
		'lloc\\Msls\\Query\\DatePostsCounterQuery'   => DatePostsCounterQuery::class,
		'lloc\\Msls\\Query\\MonthPostsCounterQuery'  => MonthPostsCounterQuery::class,
		'lloc\\Msls\\Query\\TranslatedPostIdQuery'   => TranslatedPostIdQuery::class,
		'lloc\\Msls\\Query\\YearPostsCounterQuery'   => YearPostsCounterQuery::class,

		'lloc\\Msls\\MslsFields'                     => Fields::class,
		'lloc\\Msls\\MslsGetSet'                     => GetSet::class,
		'lloc\\Msls\\MslsJson'                       => Json::class,
		'lloc\\Msls\\MslsLanguageArray'              => LanguageArray::class,
		'lloc\\Msls\\MslsMain'                       => Main::class,
		'lloc\\Msls\\MslsPlugin'                     => Plugin::class,
		'lloc\\Msls\\MslsRequest'                    => Request::class,
	);

	/**
	 * Names that never shipped before 3.0, so no third-party code can be holding them.
	 *
	 * They stay out of the eager pass: nothing can name them in a type declaration, and
	 * aliasing MslsTranslationPickerTable would drag wp-admin/includes/class-wp-list-table.php
	 * into every front-end request. The autoloader still resolves them on demand.
	 *
	 * @var array<int, string>
	 */
	public const LAZY_ONLY = array(
		'lloc\\Msls\\MslsPostListActions',
		'lloc\\Msls\\MslsRestApi',
		'lloc\\Msls\\MslsTranslationPickerPage',
		'lloc\\Msls\\MslsTranslationPickerTable',
	);

	/**
	 * Creates the aliases and installs the autoloader resolving the rest on demand.
	 *
	 * The autoloader is appended, not prepended: Composer's PSR-4 loader stays
	 * authoritative for the current class names and this one only ever runs for a name it
	 * could not resolve.
	 */
	public static function register(): void {
		spl_autoload_register(
			static function ( string $name ): void {
				if ( isset( self::MAP[ $name ] ) ) {
					class_alias( self::MAP[ $name ], $name );
				}
			}
		);

		foreach ( self::MAP as $legacy => $current ) {
			if ( in_array( $legacy, self::LAZY_ONLY, true ) ) {
				continue;
			}

			class_alias( $current, $legacy );
		}
	}
}
