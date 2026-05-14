<?php
/**
 * Backwards-compatibility aliases for the MslsOptions* classes that moved
 * from lloc\Msls\MslsOptions* (flat) to lloc\Msls\Options\Options* in 2.10.x.
 *
 * Third-party code that still references the old class names continues to work
 * because each old fully-qualified name is registered as an alias for the new one.
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use lloc\Msls\Options\Options;
use lloc\Msls\Options\Post\Post;
use lloc\Msls\Options\Query\Author;
use lloc\Msls\Options\Query\Day;
use lloc\Msls\Options\Query\Month;
use lloc\Msls\Options\Query\PostType;
use lloc\Msls\Options\Query\Query;
use lloc\Msls\Options\Query\Year;
use lloc\Msls\Options\Tax\Category;
use lloc\Msls\Options\Tax\Tax;
use lloc\Msls\Options\Tax\Term;

class_alias( Options::class, 'lloc\\Msls\\MslsOptions' );
class_alias( Post::class, 'lloc\\Msls\\MslsOptionsPost' );
class_alias( Query::class, 'lloc\\Msls\\MslsOptionsQuery' );
class_alias( Author::class, 'lloc\\Msls\\MslsOptionsQueryAuthor' );
class_alias( Day::class, 'lloc\\Msls\\MslsOptionsQueryDay' );
class_alias( Month::class, 'lloc\\Msls\\MslsOptionsQueryMonth' );
class_alias( PostType::class, 'lloc\\Msls\\MslsOptionsQueryPostType' );
class_alias( Year::class, 'lloc\\Msls\\MslsOptionsQueryYear' );
class_alias( Tax::class, 'lloc\\Msls\\MslsOptionsTax' );
class_alias( Term::class, 'lloc\\Msls\\MslsOptionsTaxTerm' );
class_alias( Category::class, 'lloc\\Msls\\MslsOptionsTaxTermCategory' );
