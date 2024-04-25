<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostFields\Duplicating;

class PostFieldsImporters extends ImportersBaseFactory {

	const TYPE = 'post-fields';

	protected $importers_map = [
		Duplicating::TYPE => Duplicating::class,
	];

	/**
	 * Returns the factory details.
	 *
	 * @return string
	 */
	public function details() {
		return (object) [
			'slug'      => static::TYPE,
			'name'      => __( 'Post Fields', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		];
	}
}
