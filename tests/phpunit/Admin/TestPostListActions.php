<?php declare( strict_types=1 );

namespace lloc\MslsTests\Admin;

use Brain\Monkey\Filters;
use lloc\Msls\Admin\PostListActions;
use lloc\MslsTests\MslsUnitTestCase;

final class TestPostListActions extends MslsUnitTestCase {

	public function test_show_button_defaults_to_true(): void {
		Filters\expectApplied( 'msls_translation_picker_button' )->once()->with( true, 'post' )->andReturnFirstArg();

		$this->assertTrue( PostListActions::show_button( 'post' ) );
	}

	public function test_show_button_can_be_filtered_off(): void {
		Filters\expectApplied( 'msls_translation_picker_button' )->once()->with( true, 'page' )->andReturn( false );

		$this->assertFalse( PostListActions::show_button( 'page' ) );
	}
}
