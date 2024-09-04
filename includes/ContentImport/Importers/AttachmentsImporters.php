<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\Attachments\Linking;

class AttachmentsImporters extends ImportersBaseFactory {

	const TYPE = 'attachments';

	/**
	 * @var array<string, string>
	 */
	protected array $importers_map = array(
		Linking::TYPE => Linking::class,
	);

	/**
	 * Returns the factory details.
	 *
	 * @return \stdClass
	 */
	public function details() {
		return (object) array(
			'slug'      => static::TYPE,
			'name'      => __( 'Image Attachments', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		);
	}
}
