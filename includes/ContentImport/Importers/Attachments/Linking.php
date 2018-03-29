<?php

namespace lloc\Msls\ContentImport\Importers\Attachments;

use lloc\Msls\ContentImport\Importers\BaseImporter;

/**
 * Class Linking
 *
 * Post attachments are just left in place in the source blog.
 *
 * @package lloc\Msls\ContentImport\Importers\Attachments
 */
class Linking extends BaseImporter {

	const TYPE = 'linking';

	/**
	 * Returns an array of information about the importer.
	 *
	 * @return \stdClass
	 */
	public static function info() {
		return (object) [
			'slug'        => static::TYPE,
			'name'        => __( 'Linking', 'multisite-language-switcher' ),
			'description' => __( 'Links the media attachments from the source post to the destination post; media attachments are not duplicated.', 'multisite-language-switcher' )
		];
	}

	public function import( array $data ) {
		$this->logger->log_information(
			'post-attachments',
			__( 'Post attachments were left in place in the source blog and linked in the destination post.', 'multisite-language-switcher' )
		);

		return parent::import( $data );
	}
}