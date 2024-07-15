<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\ImportLogger;
use lloc\Msls\ContentImport\Relations;

class BaseImporter implements Importer {

	/**
	 * @var ImportCoordinates
	 */
	public $import_coordinates;

	/**
	 * @var ImportLogger
	 */
	public $logger;

	/**
	 * @var Relations
	 */
	public $relations;

	/**
	 * BaseImporter constructor.
	 *
	 * @param ImportLogger|null $logger
	 * @param Relations|null $relations
	 */
	public function __construct(
		ImportCoordinates $import_coordinates,
		ImportLogger $logger = null,
		Relations $relations = null
	) {
		$this->import_coordinates = $import_coordinates;
		$this->logger             = $logger ?: new ImportLogger( $this->import_coordinates );
		$this->relations          = $relations ?: new Relations( $this->import_coordinates );
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function import( array $data ) {
		return $data;
	}

	/**
	 * @param ImportCoordinates $import_coordinates
	 *
	 * @return mixed
	 */
	public function set_import_coordinates( ImportCoordinates $import_coordinates ) {
		$this->import_coordinates = $import_coordinates;
	}

	/**
	 * @return ImportLogger
	 */
	public function get_logger() {
		return $this->logger;
	}

	/**
	 * @return Relations
	 */
	public function get_relations() {
		return $this->relations;
	}

	/**
	 * Returns an array of information about the importer.
	 *
	 * @return \stdClass
	 */
	public static function info() {
		return new \stdClass();
	}
}