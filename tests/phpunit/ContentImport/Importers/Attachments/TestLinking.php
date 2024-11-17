<?php

namespace lloc\MslsTests\ContentImport\Importers\Attachments;

use Brain\Monkey\Functions;
use lloc\Msls\ContentImport\ImportCoordinates;
use lloc\Msls\ContentImport\Importers\Attachments\Linking;
use lloc\MslsTests\MslsUnitTestCase;

class TestLinking extends MslsUnitTestCase {

	public function testImport(): void {
		$coordinates = \Mockery::mock( ImportCoordinates::class );

		$this->assertEquals( array(), ( new Linking( $coordinates ) )->import( array() ) );
	}

	public function testInfo(): void {
		$object = (object) array(
			'slug'        => 'linking',
			'name'        => 'Linking',
			'description' => 'Links the media attachments from the source post to the destination post; media attachments are not duplicated.',
		);
		$this->assertEquals( $object, Linking::info() );
	}
}
