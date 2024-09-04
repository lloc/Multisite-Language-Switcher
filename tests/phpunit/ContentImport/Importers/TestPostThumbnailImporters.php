<?php

namespace lloc\MslsTests\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\PostThumbnailImporters;
use lloc\MslsTests\MslsUnitTestCase;

class TestPostThumbnailImporters extends MslsUnitTestCase {

	public function testDetails(): void {
		$test = new PostThumbnailImporters();

		$expected = (object) array(
			'slug'      => 'post-thumbnail',
			'name'      => 'Featured Image',
			'importers' => array(
				'linking' => (object) array(
					'name'        => 'Linking',
					'description' => 'Links the featured image from the source post to the destination post; the image is not duplicated.',
					'slug'        => 'linking',
				),
			),
			'selected'  => 'linking',
		);

		$this->assertEquals( $expected, $test->details() );
	}
}
