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
	public function make( ImportCoordinates $import_coordinates );

	/**
	 * Returns the factory details.
	 *
	 * @return string
	 */
	public function details();

	/**
	 * Returns the slug of the default importer for this factory.
	 *
	 * @return string
	 */
	public function selected();
}