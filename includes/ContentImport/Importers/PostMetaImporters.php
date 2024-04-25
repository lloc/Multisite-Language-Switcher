<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostMeta\Duplicating;

class PostMetaImporters extends ImportersBaseFactory {

	const TYPE = 'post-meta';

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
			'name'      => __( 'Meta Fields', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		];
	}
}
