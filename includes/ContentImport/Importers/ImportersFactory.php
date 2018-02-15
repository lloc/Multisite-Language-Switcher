<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;

interface ImportersFactory {

	/**
	 * Builds the Importer that should be used depending on the import coordinates.
	 *
	 * @return Importer
	 */
	public static function make( ImportCoordinates $import_coordinates);
}