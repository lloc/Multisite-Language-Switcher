<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostMeta\Duplicating;

class PostMetaImporters extends ImportersBaseFactory {

	const TYPE = 'post-meta';

	/**
	 * @var array<string, string>
	 */
	protected array $importers_map = array(
		Duplicating::TYPE => Duplicating::class,
	);

	/**
	 * Returns the factory details.
	 *
	 * @return \stdClass
	 */
	public function details() {
		return (object) array(
			'slug'      => static::TYPE,
			'name'      => __( 'Meta Fields', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		);
	}
}
