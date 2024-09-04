<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostFields\Duplicating;

class PostFieldsImporters extends ImportersBaseFactory {

	const TYPE = 'post-fields';

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
			'name'      => __( 'Post Fields', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		);
	}
}
