<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;

interface ImportersFactory {

	/**
	 * Builds the Importer that should be used depending on the import coordinates.
	 *
	 * @param \lloc\Msls\ContentImport\ImportCoordinates $import_coordinates
	 *
	 * @return Importer
	 */
	public static function make( ImportCoordinates $import_coordinates );
}