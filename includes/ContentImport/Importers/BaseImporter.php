<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\ImportLog;
use lloc\Msls\ContentImport\Relations;

abstract class BaseImporter implements Importer {

	/**
	 * @var ImportCoordinates
	 */
	public $import_coordinates;

	/**
	 * @var ImportLog
	 */
	public $log;

	/**
	 * @var Relations
	 */
	public $relations;

	/**
	 * BaseImporter constructor.
	 *
	 * @param ImportLog|null $log
	 * @param Relations|null $relations
	 */
	public function __construct( ImportLog $log = null, Relations $relations = null ) {
		$this->log       = null !== $log ?: new ImportLog();
		$this->relations = null !== $relations ?: new Relations();
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
	 * @return ImportLog
	 */
	public function get_log() {
		return $this->log;
	}

	/**
	 * @return Relations
	 */
	public function get_relations() {
		return $this->relations;
	}
}