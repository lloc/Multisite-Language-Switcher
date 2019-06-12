<?php

namespace lloc\Msls\ContentImport\Importers;

use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\PostMeta\Duplicating;

class BadImportersFactory extends ImportersBaseFactory {
}

class DummyImportersBaseFactoryOne extends ImportersBaseFactory {

	const TYPE = 'one';

	protected $importers_map = [];
}

class DummyImportersBaseFactoryTwo extends ImportersBaseFactory {

	const TYPE = 'two';

	protected $importers_map = [
		'some-option' => Duplicating::class,
	];
}

class ImportersBaseFactoryTest extends \Msls_UnitTestCase {

	/**
	 * @var ImportCoordinates
	 */
	protected $import_coordinates;

	/**
	 * Test make will throw if the extending class is not defining its type
	 */
	public function test_make_will_throw_if_the_extending_class_is_not_defining_its_type() {
		$this->expectException( \RuntimeException::class );

		BadImportersFactory::instance()->make( $this->import_coordinates->reveal() );
	}

	/**
	 * Test will return BaseImporter if map empty
	 */
	public function test_will_return_base_importer_if_map_empty() {
		$this->assertInstanceOf(
			BaseImporter::class,
			DummyImportersBaseFactoryOne::instance()->make( $this->import_coordinates->reveal() )
		);
	}

	/**
	 * Test will return BaseImporter if map emptied
	 */
	public function test_will_return_base_importer_if_map_emptied() {
		add_filter( 'msls_content_import_two_importers_map', function () {
			return [];
		} );

		$this->assertInstanceOf(
			BaseImporter::class,
			DummyImportersBaseFactoryTwo::instance()->make( $this->import_coordinates->reveal() )
		);
	}

	/**
	 * Test will return filtered importer
	 */
	public function test_will_return_filtered_importer() {
		$importer = $this->prophesize( Importer::class )->reveal();

		add_filter( 'msls_content_import_one_importer', function () use ( $importer ) {
			return $importer;
		} );

		$this->assertSame(
			$importer,
			DummyImportersBaseFactoryOne::instance()->make( $this->import_coordinates->reveal() )
		);
	}

	/**
	 * Test will return base importer if slug missing from map
	 */
	public function test_will_return_base_importer_if_slug_missing_from_map() {
		$this->import_coordinates->get_importer_for( 'two' )->willReturn( 'not-there' );

		$this->assertInstanceOf(
			BaseImporter::class,
			DummyImportersBaseFactoryTwo::instance()->make( $this->import_coordinates->reveal() )
		);
	}

	/**
	 * Test will return the mapped importer
	 */
	public function test_will_return_the_mapped_importer() {
		$this->import_coordinates->get_importer_for( 'two' )->willReturn( 'some-option' );

		$this->assertInstanceOf(
			Duplicating::class,
			DummyImportersBaseFactoryTwo::instance()->make( $this->import_coordinates->reveal() )
		);
	}

	public function setUp() {
		parent::setUp();
		$this->import_coordinates = $this->prophesize( ImportCoordinates::class );
	}
}
