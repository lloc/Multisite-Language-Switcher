<?php

namespace lloc\MslsTests\ContentImport\Importers;

use brain\Monkey\Filters;
use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\PostFields\Duplicating;
use lloc\Msls\ContentImport\Importers\PostFieldsImporters;
use lloc\MslsTests\MslsUnitTestCase;

final class TestPostFieldsImporters extends MslsUnitTestCase {

	public function testMake(): void {
		$importer = \Mockery::mock( Duplicating::class );

		$test = new PostFieldsImporters();

		Filters\expectApplied( 'msls_content_import_post-fields_importer' )->once()->andReturn( $importer );

		$this->assertEquals( $importer, $test->make( new ImportCoordinates() ) );
	}

	public function testDetails(): void {
		$test = new PostFieldsImporters();

		$expected = (object) array(
			'slug'      => 'post-fields',
			'name'      => 'Post Fields',
			'importers' => array(
				'duplicating' => (object) array(
					'name'        => 'Duplicating',
					'description' => 'Copies the source post fields to the destination.',
					'slug'        => 'duplicating',
				),
			),
			'selected'  => 'duplicating',
		);

		$this->assertEquals( $expected, $test->details() );
	}
}
