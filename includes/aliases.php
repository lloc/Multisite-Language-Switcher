<?php
/**
 * Backwards-compatibility aliases for the classes that were restructured
 * from the flat lloc\Msls\Msls* layout into per-concern sub-namespaces
 * (Options\, Link\, Frontend\, ContentTypes\) and for the interfaces that
 * moved alongside their implementations during 2.10.x.
 *
 * Third-party code that still references the old fully-qualified names
 * continues to work because each old name is registered as an alias for
 * the new one.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

class_alias( Options::class, 'lloc\\Msls\\MslsOptions' );
class_alias( Post::class, 'lloc\\Msls\\MslsOptionsPost' );
class_alias( Query::class, 'lloc\\Msls\\MslsOptionsQuery' );
class_alias( Author::class, 'lloc\\Msls\\MslsOptionsQueryAuthor' );
class_alias( Day::class, 'lloc\\Msls\\MslsOptionsQueryDay' );
class_alias( Month::class, 'lloc\\Msls\\MslsOptionsQueryMonth' );
class_alias( QueryPostType::class, 'lloc\\Msls\\MslsOptionsQueryPostType' );
class_alias( Year::class, 'lloc\\Msls\\MslsOptionsQueryYear' );

class_alias( Tax::class, 'lloc\\Msls\\MslsOptionsTax' );
class_alias( Term::class, 'lloc\\Msls\\MslsOptionsTaxTerm' );
class_alias( Category::class, 'lloc\\Msls\\MslsOptionsTaxTermCategory' );

class_alias( Link::class, 'lloc\\Msls\\MslsLink' );
class_alias( ImageOnly::class, 'lloc\\Msls\\MslsLinkImageOnly' );
class_alias( TextImage::class, 'lloc\\Msls\\MslsLinkTextImage' );
class_alias( TextOnly::class, 'lloc\\Msls\\MslsLinkTextOnly' );

class_alias( LinkInterface::class, 'lloc\\Msls\\LinkInterface' );
class_alias( OptionsInterface::class, 'lloc\\Msls\\OptionsInterface' );
class_alias( OptionsTaxInterface::class, 'lloc\\Msls\\OptionsTaxInterface' );

class_alias( Output::class, 'lloc\\Msls\\MslsOutput' );
class_alias( Widget::class, 'lloc\\Msls\\MslsWidget' );
class_alias( Block::class, 'lloc\\Msls\\MslsBlock' );
class_alias( ShortCode::class, 'lloc\\Msls\\MslsShortCode' );
class_alias( ContentFilter::class, 'lloc\\Msls\\MslsContentFilter' );

class_alias( ContentTypes::class, 'lloc\\Msls\\MslsContentTypes' );
class_alias( ContentPostType::class, 'lloc\\Msls\\MslsPostType' );
class_alias( Taxonomy::class, 'lloc\\Msls\\MslsTaxonomy' );

class_alias( Admin::class, 'lloc\\Msls\\MslsAdmin' );
class_alias( Bar::class, 'lloc\\Msls\\MslsAdminBar' );
class_alias( Icon::class, 'lloc\\Msls\\MslsAdminIcon' );
class_alias( IconTaxonomy::class, 'lloc\\Msls\\MslsAdminIconTaxonomy' );
class_alias( CustomColumn::class, 'lloc\\Msls\\MslsCustomColumn' );
class_alias( CustomColumnTaxonomy::class, 'lloc\\Msls\\MslsCustomColumnTaxonomy' );
class_alias( CustomFilter::class, 'lloc\\Msls\\MslsCustomFilter' );
class_alias( MetaBox::class, 'lloc\\Msls\\MslsMetaBox' );
class_alias( PostListActions::class, 'lloc\\Msls\\MslsPostListActions' );

class_alias( TranslationPickerPage::class, 'lloc\\Msls\\MslsTranslationPickerPage' );
class_alias( TranslationPickerTable::class, 'lloc\\Msls\\MslsTranslationPickerTable' );

class_alias( Blog::class, 'lloc\\Msls\\MslsBlog' );
class_alias( BlogCollection::class, 'lloc\\Msls\\MslsBlogCollection' );

class_alias( Cli::class, 'lloc\\Msls\\MslsCli' );

class_alias( SqlCacher::class, 'lloc\\Msls\\MslsSqlCacher' );

class_alias( Registry::class, 'lloc\\Msls\\MslsRegistry' );
class_alias( RegistryInstance::class, 'lloc\\Msls\\MslsRegistryInstance' );

class_alias( PostTag::class, 'lloc\\Msls\\MslsPostTag' );
class_alias( PostTagClassic::class, 'lloc\\Msls\\MslsPostTagClassic' );

class_alias( RestApi::class, 'lloc\\Msls\\MslsRestApi' );

class_alias( AuthorPostsCounterQuery::class, 'lloc\\Msls\\Query\\AuthorPostsCounterQuery' );
class_alias( BlogsInNetworkQuery::class, 'lloc\\Msls\\Query\\BlogsInNetworkQuery' );
class_alias( CleanupOptionsQuery::class, 'lloc\\Msls\\Query\\CleanupOptionsQuery' );
class_alias( DatePostsCounterQuery::class, 'lloc\\Msls\\Query\\DatePostsCounterQuery' );
class_alias( MonthPostsCounterQuery::class, 'lloc\\Msls\\Query\\MonthPostsCounterQuery' );
class_alias( TranslatedPostIdQuery::class, 'lloc\\Msls\\Query\\TranslatedPostIdQuery' );
class_alias( YearPostsCounterQuery::class, 'lloc\\Msls\\Query\\YearPostsCounterQuery' );

class_alias( Fields::class, 'lloc\\Msls\\MslsFields' );
class_alias( GetSet::class, 'lloc\\Msls\\MslsGetSet' );
class_alias( Json::class, 'lloc\\Msls\\MslsJson' );
class_alias( LanguageArray::class, 'lloc\\Msls\\MslsLanguageArray' );
class_alias( Main::class, 'lloc\\Msls\\MslsMain' );
class_alias( Plugin::class, 'lloc\\Msls\\MslsPlugin' );
class_alias( Request::class, 'lloc\\Msls\\MslsRequest' );
