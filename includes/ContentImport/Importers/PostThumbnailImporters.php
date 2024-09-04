<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostThumbnail\Linking;

class PostThumbnailImporters extends ImportersBaseFactory {

	const TYPE = 'post-thumbnail';

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
			'name'      => __( 'Featured Image', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		);
	}
}
