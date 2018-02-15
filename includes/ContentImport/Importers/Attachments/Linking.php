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

	public function import( array $data ) {
		$this->logger->log_information(
			'post-thumbnail',
			__( 'Post attachments were left in place in the source blog and linked in the destination post.', 'multisite-language-switcher' )
		);

		return parent::import( $data );
	}
}