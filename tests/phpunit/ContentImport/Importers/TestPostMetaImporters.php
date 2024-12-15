<?php

namespace lloc\MslsTests\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostMetaImporters;
use lloc\MslsTests\MslsUnitTestCase;

final class TestPostMetaImporters extends MslsUnitTestCase {

	public function testDetails(): void {
		$test = new PostMetaImporters();

		$expected = (object) array(
			'slug'      => 'post-meta',
			'name'      => 'Meta Fields',
			'importers' => array(
				'duplicating' => (object) array(
					'name'        => 'Duplicating',
					'description' => 'Copies the source post meta to the destination.',
					'slug'        => 'duplicating',
				),
			),
			'selected'  => 'duplicating',
		);

		$this->assertEquals( $expected, $test->details() );
	}
}
