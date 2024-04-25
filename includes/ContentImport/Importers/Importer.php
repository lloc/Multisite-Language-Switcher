<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\Msls\ContentImport\Relations;

interface Importer {

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function import( array $data );

	/**
	 * @param ImportCoordinates $import_coordinates
	 *
	 * @return mixed
	 */
	public function set_import_coordinates( ImportCoordinates $import_coordinates );

	/**
	 * @return ImportLogger
	 */
	public function get_logger();

	/**
	 * @return Relations
	 */
	public function get_relations();

	/**
	 * Returns an array of information about the importer.
	 *
	 * @return \stdClass
	 */
	public static function info();
}
