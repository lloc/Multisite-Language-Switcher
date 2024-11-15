<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\MslsRegistryInstance;

abstract class ImportersBaseFactory extends MslsRegistryInstance implements ImportersFactory {

	/**
	 * The type of this importers factory; should be overridden by child classes.
	 */
	const TYPE = 'none';

	/**
	 * @var array<string, string> An array defining the slug and Importer class relationships in
	 *            the shape [ <slug> => <importer-class> ]
	 */
	protected array $importers_map = array();

	/**
	 * @return Importer
	 */
	public function make( ImportCoordinates $import_coordinates ) {
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
		 * @param ?Importer         $importer
		 * @param ImportCoordinates $import_coordinates
		 */
		$importer = apply_filters( "msls_content_import_{$type}_importer", null, $import_coordinates );
		if ( $importer instanceof Importer ) {
			return $importer;
		}

		/**
		 * Filters the importers map.
		 *
		 * @param array $map A map of importers in the shape [ <importer-slug> => <importer-class> ]
		 * @param ImportCoordinates $import_coordinates
		 *
		 * @since TBD
		 */
		$map = apply_filters( "msls_content_import_{$type}_importers_map", $this->importers_map, $import_coordinates );

		$first = count( $map ) > 0 ? reset( $map ) : null;
		$slug  = $import_coordinates->get_importer_for( $type ) ?: $first;

		// if there is some incoherence return the null-doing base importer
		$class = ! empty( $slug ) && isset( $map[ $slug ] ) ? $map[ $slug ] : BaseImporter::class;

		return new $class( $import_coordinates );
	}

	/**
	 * Returns the factory details.
	 *
	 * @return \stdClass
	 */
	public function details() {
		return (object) array(
			'name'      => 'Base Factory',
			'importers' => array(),
		);
	}

	/**
	 * Returns the slug of the selected importer for this factory.
	 *
	 * @return string
	 */
	public function selected() {
		$slug     = static::TYPE;
		$selected = array_keys( $this->importers_map )[0];
		$instance = $this;

		/**
		 * Filters the selected importer that among the available ones.
		 *
		 * @param string $selected The selected importer slug.
		 * @param ImportersFactory $instance
		 */
		$selected = apply_filters( "msls_content_import_{$slug}_selected", $selected, $instance );

		return $selected;
	}

	/**
	 * @return array<string, \stdClass>
	 */
	protected function importers_info(): array {
		return array_combine(
			array_keys( $this->importers_map ),
			array_map(
				function ( $importer_class ) {
					return $importer_class::info();
				},
				$this->importers_map
			)
		);
	}
}
