<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostFields\Duplicating;

class PostFieldsImporters extends ImportersBaseFactory {

	const TYPE = 'post-fields';

	protected static $importers_map = [
		'duplicating' => Duplicating::class,
	];
}