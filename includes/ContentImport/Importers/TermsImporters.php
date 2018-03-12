<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\Terms\ShallowDuplicating;

class TermsImporters extends ImportersBaseFactory {

	const TYPE = 'terms';

	protected $importers_map = [
		ShallowDuplicating::TYPE => ShallowDuplicating::class,
	];

	/**
	 * Returns the factory details.
	 *
	 * @return string
	 */
	public function details() {
		return (object) [
			'slug' => static::TYPE,
			'name' => __( 'Taxonomy Terms', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		];
	}
}
