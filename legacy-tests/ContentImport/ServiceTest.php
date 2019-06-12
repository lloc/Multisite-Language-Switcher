<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\MslsOptions;
use lloc\Msls\MslsRegistry;

class ServiceTest extends \Msls_UnitTestCase {

	/**
	 * Test register when content import deactivated
	 */
	public function test_register_when_content_import_deactivated() {
		$blog_id = $this->factory->blog->create();
		update_blog_option( $blog_id, 'msls', [] );
		MslsRegistry::set_object( MslsOptions::class, null );

		switch_to_blog( $blog_id );

		$obj = Service::instance();

		$this->assertFalse( $obj->register() );
	}

	/**
	 * Test register when content import activated
	 */
	public function test_register_when_content_import_activated() {
		$blog_id = $this->factory->blog->create();
		update_blog_option( $blog_id, 'msls', [
			'activate_content_import' => '1',
		] );
		MslsRegistry::set_object( MslsOptions::class, null );

		switch_to_blog( $blog_id );

		$obj = Service::instance();

		$this->assertTrue( $obj->register() );
	}
}
