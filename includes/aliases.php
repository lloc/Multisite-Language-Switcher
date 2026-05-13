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
use lloc\Msls\Options\OptionsPost;
use lloc\Msls\Options\OptionsQuery;
use lloc\Msls\Options\OptionsQueryAuthor;
use lloc\Msls\Options\OptionsQueryDay;
use lloc\Msls\Options\OptionsQueryMonth;
use lloc\Msls\Options\OptionsQueryPostType;
use lloc\Msls\Options\OptionsQueryYear;
use lloc\Msls\Options\OptionsTax;
use lloc\Msls\Options\OptionsTaxTerm;
use lloc\Msls\Options\OptionsTaxTermCategory;

class_alias( Options::class, 'lloc\\Msls\\MslsOptions' );
class_alias( OptionsPost::class, 'lloc\\Msls\\MslsOptionsPost' );
class_alias( OptionsQuery::class, 'lloc\\Msls\\MslsOptionsQuery' );
class_alias( OptionsQueryAuthor::class, 'lloc\\Msls\\MslsOptionsQueryAuthor' );
class_alias( OptionsQueryDay::class, 'lloc\\Msls\\MslsOptionsQueryDay' );
class_alias( OptionsQueryMonth::class, 'lloc\\Msls\\MslsOptionsQueryMonth' );
class_alias( OptionsQueryPostType::class, 'lloc\\Msls\\MslsOptionsQueryPostType' );
class_alias( OptionsQueryYear::class, 'lloc\\Msls\\MslsOptionsQueryYear' );
class_alias( OptionsTax::class, 'lloc\\Msls\\MslsOptionsTax' );
class_alias( OptionsTaxTerm::class, 'lloc\\Msls\\MslsOptionsTaxTerm' );
class_alias( OptionsTaxTermCategory::class, 'lloc\\Msls\\MslsOptionsTaxTermCategory' );
