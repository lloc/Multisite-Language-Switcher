<?php

namespace lloc\MslsTests\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\AttachmentsImporters;
use lloc\MslsTests\MslsUnitTestCase;

final class TestAttachmentsImporters extends MslsUnitTestCase {


	public function testDetails(): void {
		$test = new AttachmentsImporters();

		$expected = (object) array(
			'slug'      => 'attachments',
			'name'      => 'Image Attachments',
			'importers' => array(
				'linking' => (object) array(
					'name'        => 'Linking',
					'description' => 'Links the media attachments from the source post to the destination post; media attachments are not duplicated.',
					'slug'        => 'linking',
				),
			),
			'selected'  => 'linking',
		);

		$this->assertEquals( $expected, $test->details() );
	}
}
