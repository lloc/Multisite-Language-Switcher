<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\Attachments\Linking;

class AttachmentsImporters implements ImportersFactory {

	/**
	 * @return Importer
	 */
	public static function make( ImportCoordinates $import_coordinates ) {
		/**
		 * Filters the importer that should be used to import attachments.
		 *
		 * Returning an Importer instance here will force the class to return that.
		 *
		 * @since TBD
		 *
		 * @param                   $importer Importer
		 * @param ImportCoordinates $import_coordinates
		 */
		$importer = apply_filters( 'msls_content_import_attachments_importer', null, $import_coordinates );
		if ( null !== $importer ) {
			return $importer;
		}

		// @todo here use an option from UI to select built-in importer
		return new Linking( $import_coordinates );
	}
}