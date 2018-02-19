<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\Terms\ShallowDuplicating;

class TermsImporters extends ImportersBaseFactory {

	const TYPE = 'terms';

	protected static $importers_map = [
		'shallow-duplicating' => ShallowDuplicating::class,
	];
}