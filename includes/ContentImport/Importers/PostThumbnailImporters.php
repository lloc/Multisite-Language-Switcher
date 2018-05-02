<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostThumbnail\Linking;

class PostThumbnailImporters extends ImportersBaseFactory {

	const TYPE = 'post-thumbnail';

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
			'slug' => static::TYPE,
			'name' => __( 'Featured Image', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		];
	}
}
