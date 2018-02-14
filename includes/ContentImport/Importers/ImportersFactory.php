<?php

namespace lloc\Msls\ContentImport\Importers;

interface ImportersFactory {

	/**
	 * @return Importer
	 */
	public static function make();
}