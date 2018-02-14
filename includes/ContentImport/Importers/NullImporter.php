<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\ImportLog;
use lloc\Msls\ContentImport\Relations;

class NullImporter implements Importer {

	public function import( array $data ) {
		// no-op
	}

	/**
	 * @param ImportCoordinates $import_coordinates
	 *
	 * @return mixed
	 */
	public function set_import_coordinates( ImportCoordinates $import_coordinates ) {
		//no-op
	}

	/**
	 * @return ImportLog
	 */
	public function get_log() {
		//no-op
	}

	/**
	 * @return Relations
	 */
	public function get_relations() {
		//no-op
	}
}