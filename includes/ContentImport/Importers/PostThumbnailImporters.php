<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostThumbnail\Linking;

class PostThumbnailImporters extends ImportersBaseFactory {

	const TYPE = 'post-thumbnail';

	protected static $importers_map = [
		'duplicating' => Linking::class,
	];
}