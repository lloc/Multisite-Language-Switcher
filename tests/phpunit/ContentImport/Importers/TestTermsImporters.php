<?php

namespace lloc\MslsTests\ContentImport\Importers;

use lloc\Msls\ContentImport\Importers\TermsImporters;
use lloc\MslsTests\MslsUnitTestCase;

final class TestTermsImporters extends MslsUnitTestCase {

	public function testDetails(): void {
		$test = new TermsImporters();

		$expected = (object) array(
			'slug'      => 'terms',
			'name'      => 'Taxonomy Terms',
			'importers' => array(
				'shallow-duplicating' => (object) array(
					'name'        => 'Shallow Duplicating',
					'description' => 'Shallow (one level deep) duplication or assignment of the source post taxonomy terms to the destination post.',
					'slug'        => 'shallow-duplicating',
				),
			),
			'selected'  => 'shallow-duplicating',
		);

		$this->assertEquals( $expected, $test->details() );
	}
}
