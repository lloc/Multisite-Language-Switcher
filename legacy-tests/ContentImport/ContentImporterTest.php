<?php

namespace lloc\Msls\ContentImport;

use lloc\Msls\ContentImport\Importers\Importer;
use lloc\Msls\MslsMain;
use lloc\Msls\MslsOptions;
use Prophecy\Argument;

class ContentImporterTest extends \Msls_UnitTestCase {

	/**
	 * @var \lloc\Msls\MslsMain
	 */
	protected $main;

	/**
	 * @var \lloc\Msls\ContentImport\ImportLogger
	 */
	protected $logger;

	/**
	 * @var \lloc\Msls\ContentImport\Relations
	 */
	protected $relations;

	/**
	 * @test
	 * it should be instantiatable
	 */
	public function it_should_be_instantiatable() {
		$sut = $this->make_instance();

		$this->assertInstanceOf( ContentImporter::class, $sut );
	}

	/**
	 * @return ContentImporter
	 */
	private function make_instance() {
		$instance = new ContentImporter( $this->main->reveal() );
		$instance->set_logger( $this->logger->reveal() );
		$instance->set_relations( $this->relations->reveal() );

		return $instance;
	}

	public function filter_empty_inputs() {
		return [
			// $input, $nonce_verified, $POST_vars, $expected
			[ 'foo', false, [ 'msls_import' => 'some-value' ], 'foo' ],
			[ 'foo', true, [], 'foo' ],
			[ 'foo', true, [ 'msls_import' => 'some-value' ], false ],
		];
	}

	/**
	 * @dataProvider filter_empty_inputs
	 */
	public function test_filter_empty( $input, $nonce_verified, $POST_vars, $expected ) {
		$this->main->verify_nonce()->willReturn( $nonce_verified );
		unset( $_POST['msls_import'] );
		$_POST = array_merge( $_POST, $POST_vars );

		$obj = $this->make_instance();

		$this->assertEquals( $expected, $obj->filter_empty( $input ) );
	}

	public function import_content_inputs() {
		return [
			[ 'en_US', 'de_DE' ],
			[ 'de_DE', 'en_US' ],
		];
	}

	/**
	 * @dataProvider import_content_inputs
	 */
	public function test_import_content( $source_lang, $dest_lang ) {
		$source_blog_id = $this->factory->blog->create();
		$dest_blog_id   = $this->factory->blog->create();

		switch_to_blog( $source_blog_id );
		$source_post    = $this->factory->post->create_and_get();
		$source_post_id = $source_post->ID;
		update_option( 'WPLANG', $source_lang );

		switch_to_blog( $dest_blog_id );
		$dest_post_id = $this->factory->post->create();
		update_option( 'WPLANG', $dest_lang );

		$import_coordinates                 = new ImportCoordinates();
		$import_coordinates->source_blog_id = $source_blog_id;
		$import_coordinates->source_post_id = $source_post_id;
		$import_coordinates->dest_blog_id   = $dest_blog_id;
		$import_coordinates->dest_post_id   = $dest_post_id;
		$import_coordinates->source_post    = $source_post;
		$import_coordinates->source_lang    = $source_lang;
		$import_coordinates->dest_lang      = $dest_lang;
		$data                               = [ 'foo' => 'bar' ];
		$importer                           = $this->prophesize( Importer::class );
		$importer->import( $data )
		         ->willReturn( $data );
		$importer->get_logger()->willReturn( null );
		$importer->get_relations()->willReturn( null );

		$this->relations->should_create( Argument::type( MslsOptions::class ), $dest_lang, $dest_post_id )
		                ->shouldBeCalled();
		$this->logger->merge( null )->shouldBeCalled();
		$this->logger->save()->shouldBeCalled();
		$this->relations->merge( null )->shouldBeCalled();
		$this->relations->create()->shouldBeCalled();

		add_filter( 'msls_content_import_importers', function () use ( $importer ) {
			return [
				'test-importer' => $importer->reveal(),
			];
		} );

		$obj          = $this->make_instance();
		$updated_data = $obj->import_content( $import_coordinates, $data );

		$this->assertEquals( $data, $updated_data );
	}

	public function test_import_content_with_wrong_coordinates() {
		$data               = [ 'foo' => 'bar' ];
		$obj                = $this->make_instance();
		$import_coordinates = $this->prophesize( ImportCoordinates::class );
		$import_coordinates->validate()->willReturn( false );

		$updated_data = $obj->import_content( $import_coordinates->reveal(), $data );

		$this->assertEquals( $data, $updated_data );
	}

	public function test_content_import_without_importers() {
		$source_lang = 'de_DE';
		$dest_lang   = 'en_US';

		$source_blog_id = $this->factory->blog->create();
		$dest_blog_id   = $this->factory->blog->create();

		switch_to_blog( $source_blog_id );
		$source_post    = $this->factory->post->create_and_get();
		$source_post_id = $source_post->ID;
		update_option( 'WPLANG', $source_lang );

		switch_to_blog( $dest_blog_id );
		$dest_post_id = $this->factory->post->create();
		update_option( 'WPLANG', $dest_lang );

		$import_coordinates                 = new ImportCoordinates();
		$import_coordinates->source_blog_id = $source_blog_id;
		$import_coordinates->source_post_id = $source_post_id;
		$import_coordinates->dest_blog_id   = $dest_blog_id;
		$import_coordinates->dest_post_id   = $dest_post_id;
		$import_coordinates->source_post    = $source_post;
		$import_coordinates->source_lang    = $source_lang;
		$import_coordinates->dest_lang      = $dest_lang;
		$data                               = [ 'foo' => 'bar' ];
		$importer                           = $this->prophesize( Importer::class );
		$this->relations->should_create( Argument::type( MslsOptions::class ), $dest_lang, $dest_post_id )
		                ->shouldNotBeCalled();
		$this->logger->merge( null )->shouldNotBeCalled();
		$this->logger->save()->shouldNotBeCalled();
		$this->relations->merge( null )->shouldNotBeCalled();
		$this->relations->create()->shouldNotBeCalled();

		add_filter( 'msls_content_import_importers', function () use ( $importer ) {
			return [];
		} );

		$obj          = $this->make_instance();
		$updated_data = $obj->import_content( $import_coordinates, $data );

		$this->assertEquals( $data, $updated_data );
	}

	public function parse_sources_inputs() {
		return [
			[ null, false ],
			[ 'foo', false ],
			[ 'foo|bar', false ],
			[ '23|bar', false ],
			[ 'foo|89', false ],
			[ '23|89', [ 23, 89 ] ],
		];
	}

	/**
	 * @dataProvider parse_sources_inputs
	 */
	public function test_parse_sources( $sources, $expected ) {
		$obj = $this->make_instance();

		unset( $_POST['msls_import'] );
		if ( null !== $sources ) {
			$_POST['msls_import'] = $sources;
		}

		$this->assertEquals( $expected, $obj->parse_sources() );
	}

	public function test_on_wp_insert_post_with_source_and_current_same() {
		$source_lang = 'de_DE';
		$dest_lang   = 'en_US';

		$source_blog_id = $this->factory->blog->create();
		$dest_blog_id   = $this->factory->blog->create();

		switch_to_blog( $source_blog_id );
		$source_post    = $this->factory->post->create_and_get();
		$source_post_id = $source_post->ID;
		update_option( 'WPLANG', $source_lang );

		switch_to_blog( $dest_blog_id );
		$dest_post_id = $this->factory->post->create();
		update_option( 'WPLANG', $dest_lang );

		$data     = [ 'foo' => 'bar' ];
		$importer = $this->prophesize( Importer::class );

		$_POST['msls_import'] = "{$source_blog_id}|{$source_post_id}";

		$this->relations->should_create( Argument::type( MslsOptions::class ), $dest_lang, $dest_post_id )
		                ->shouldNotBeCalled();
		$this->logger->merge( null )->shouldNotBeCalled();
		$this->logger->save()->shouldNotBeCalled();
		$this->relations->merge( null )->shouldNotBeCalled();
		$this->relations->create()->shouldNotBeCalled();

		add_filter( 'msls_content_import_importers', function () use ( $importer ) {
			return [
				'test-importer' => $importer->reveal(),
			];
		} );

		$obj = $this->make_instance();

		switch_to_blog( $source_blog_id );
		$updated_data = $obj->handle_import( $data );

		$this->assertEquals( $data, $updated_data );
	}

	function setUp() {
		parent::setUp();
		$this->main      = $this->prophesize( MslsMain::class );
		$this->logger    = $this->prophesize( ImportLogger::class );
		$this->relations = $this->prophesize( Relations::class );
	}
}
