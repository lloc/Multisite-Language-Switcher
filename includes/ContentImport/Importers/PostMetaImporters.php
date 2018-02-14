<?php
namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostFields\Duplicating;

class PostMetaImporters implements ImportersFactory {

	/**
	 * @return Importer
	 */
	public static function make() {
		$importer = apply_filters( 'msls_content_import_post_meta_importer', null );
		if ( null !== $importer ) {
			return $importer;
		}

		// @todo here use an option from UI to select built-in importer
		return new Duplicating();
	}
}