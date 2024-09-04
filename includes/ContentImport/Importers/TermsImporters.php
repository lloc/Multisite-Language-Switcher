<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\Terms\ShallowDuplicating;

class TermsImporters extends ImportersBaseFactory {

	const TYPE = 'terms';

	/**
	 * @var array<string, string>
	 */
	protected array $importers_map = array(
		ShallowDuplicating::TYPE => ShallowDuplicating::class,
	);

	/**
	 * Returns the factory details.
	 *
	 * @return \stdClass
	 */
	public function details() {
		return (object) array(
			'slug'      => static::TYPE,
			'name'      => __( 'Taxonomy Terms', 'multisite-language-switcher' ),
			'importers' => $this->importers_info(),
			'selected'  => $this->selected(),
		);
	}
}
