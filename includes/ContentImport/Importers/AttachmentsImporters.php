<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\Attachments\Linking;

class AttachmentsImporters extends ImportersBaseFactory {

	const TYPE = 'attachments';

	protected static $importers_map = [
		'linking' => Linking::class,
	];
}