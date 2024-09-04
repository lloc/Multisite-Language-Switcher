<?php

namespace lloc\MslsTests\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostFieldsImporters;
use lloc\MslsTests\MslsUnitTestCase;

class TestPostFieldsImporters extends MslsUnitTestCase {

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
