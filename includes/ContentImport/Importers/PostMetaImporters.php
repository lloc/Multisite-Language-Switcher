<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostMeta\Duplicating;

class PostMetaImporters extends ImportersBaseFactory {

	const TYPE = 'post-meta';

	protected static $importers_map = [
		'duplicating' => Duplicating::class,
	];
}