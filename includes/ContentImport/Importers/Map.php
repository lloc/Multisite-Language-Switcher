<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\MslsRegistryInstance;

class Map extends MslsRegistryInstance {
	/**
	 * Builds and returns an array of importers for the specified import coordinates.
	 *
	 * @param ImportCoordinates $import_coordinates
	 *
	 * @return array An array of importer instances in the shape [ <string: slug> => <Importer: $importer> ]
	 */
	public function make( ImportCoordinates $import_coordinates ) {
		$importers = array_map( function ( $factory ) use ( $import_coordinates ) {
			/** @var ImportersFactory $factory */
			return $factory->make( $import_coordinates );
		}, $this->factories() );

		/**
		 * Filters the map of importers that should be used.
		 *
		 * While the filter `msls_content_import_importers_factories_map` will allow filtering the factories this
		 * will directly modify the built importers.
		 *
		 * @param array $importers An array of importers in the shape [ <string: $slug> => <Importer: $importer> ]
		 * @param ImportCoordinates $import_coordinates
		 *
		 * @see Map::factories()
		 */
		$importers = apply_filters( 'msls_content_import_importers_map', $importers, $import_coordinates );

		return $importers;
	}

	/**
	 * Returns a filtered list of factories that will provide the importers.
	 *
	 * @since TBD
	 *
	 * @return array An associative array in the shape [ <string: $slug> => <ImportersFactory: $factory> ]
	 */
	public function factories() {
		$map = [
			'post-fields'    => PostFieldsImporters::instance(),
			'post-meta'      => PostMetaImporters::instance(),
			'terms'          => TermsImporters::instance(),
			'post-thumbnail' => PostThumbnailImporters::instance(),
			'attachments'    => AttachmentsImporters::instance(),
		];

		/**
		 * Filters the map of importer factories that should be used to build the importers.
		 *
		 * While the filter `msls_content_import_importers_map` will allow filtering the importers after they have been built
		 * by the factories this filter acts before allowing the modification of the factories before any importer is built.
		 *
		 * @param array $importers An array of importer factories in the shape [ <string: $slug> => <ImportersFactory: $factory> ]
		 *
		 * @see Map::make()
		 */
		$map = apply_filters( 'msls_content_import_importers_factories_map', $map );

		return $map;
	}
}