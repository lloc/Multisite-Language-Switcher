<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\Attachments\Linking;

class AttachmentsImporters extends ImportersBaseFactory {

	const TYPE = 'attachments';

	protected $importers_map = [
		Linking::TYPE => Linking::class,
	];

	/**
	 * Returns the factory details.
	 *
	 * @return string
	 */
	public function details() {
		return (object) [
			'slug'      => static::TYPE,
			'name'      => __( 'Image Attachments', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		];
	}
}
