<?php
namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\ImportLog;
use lloc\Msls\ContentImport\Relations;

interface Importer {

	public function import( array $data );

	/**
	 * @param ImportCoordinates $import_coordinates
	 *
	 * @return mixed
	 */
	public function set_import_coordinates( ImportCoordinates $import_coordinates );

	/**
	 * @return ImportLog
	 */
	public function get_log();

	/**
	 * @return Relations
	 */
	public function get_relations();
}