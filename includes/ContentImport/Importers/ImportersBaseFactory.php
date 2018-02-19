<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;

abstract class ImportersBaseFactory implements ImportersFactory {

	/**
	 * The type of this importers factory; should be overridden by child classes.
	 */
	const TYPE = 'none';

	/**
	 * @var array An array defining the slug and Importer class relationships in
	 *            the shape [ <slug> => <importer-class> ]
	 */
	protected static $importers_map = [];

	/**
	 * @return Importer
	 */
	public static function make( ImportCoordinates $import_coordinates ) {
		if ( static::TYPE === self::TYPE ) {
			// this is a developer-land exception, no need to localize it
			throw new \RuntimeException( 'Importers factories should define their own type' );
		}

		$type = static::TYPE;

		/**
		 * Filters the importer that should be used to import this factory type of content.
		 *
		 * Returning an Importer instance here will force the class to return that.
		 *
		 * @since TBD
		 *
		 * @param                   $importer Importer
		 * @param ImportCoordinates $import_coordinates
		 */
		$importer = apply_filters( "msls_content_import_{$type}_importer", null, $import_coordinates );
		if ( $importer instanceof Importer ) {
			return $importer;
		}

		self::$importers_map;

		/**
		 * Filters the importers map.
		 *
		 * @since TBD
		 *
		 * @param array             $map A map of importers in the shape [ <importer-slug> => <importer-class> ]
		 * @param ImportCoordinates $import_coordinates
		 */
		$map = apply_filters( "msls_content_import_{$type}_importers_map", static::$importers_map, $import_coordinates );

		$first = count( $map ) > 0 ? reset( $map ) : null;
		$slug  = $import_coordinates->get_importer_for( $type ) ?: $first;

		// if there is some incoherence return the null-doing base importer
		$class = ! empty( $slug ) && isset( $map[ $slug ] )
			? $map[ $slug ]
			: BaseImporter::class;

		return new $class( $import_coordinates );
	}
}