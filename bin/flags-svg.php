<?php

/**
 * Reads a previously retrieved file from wordpress.org
 *
 * http://api.wordpress.org/translations/core/1.0/
 *
 * and transforms it into a hashmap with language as key and the name
 * of the flag-icon as value.
 *
 * Prepare with:
 *
 * `wget http://api.wordpress.org/translations/core/1.0/ -O ./build/translations.json`
 *
 * Copyright 2013  Dennis Ploetner  (email : re@lloc.de)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
$content    = file_get_contents( 'build/translations.json' );
$json       = json_decode( $content );
$glob       = glob( 'assets/css-flags/flags/4x3/*.svg' );
$exceptions = array(
	'ca'             => 'es-ca',
	'eo'             => 'eu',
	'cy'             => 'gb-wls',
	'gd'             => 'gb-sct',
	'af'             => 'za',
	'el'             => 'gr',
	'et'             => 'ee',
	'ja'             => 'jp',
	'uk'             => 'ua',
	'as'             => 'in',
	'az'             => 'az',
	'bo'             => 'cn',
	'eu'             => 'es',
	'fi'             => 'fi',
	'gu'             => 'in',
	'hr'             => 'hr',
	'hy'             => 'am',
	'kk'             => 'kz',
	'km'             => 'kh',
	'lo'             => 'la',
	'lv'             => 'lv',
	'mn'             => 'mn',
	'mr'             => 'in',
	'ps'             => 'af',
	'sq'             => 'al',
	'te'             => 'in',
	'th'             => 'th',
	'tl'             => 'ph',
	'ur'             => 'pk',
	'vi'             => 'vn',
	'ary'            => 'ma',
	'azb'            => 'az',
	'bel'            => 'by',
	'ceb'            => 'ph',
	'dzo'            => 'bt',
	'fur'            => 'it',
	'haz'            => 'af',
	'kab'            => 'dz',
	'ckb'            => 'iq',
	'oci'            => 'es-ca',
	'rhg'            => '', // Rohingya
	'sah'            => 'ru',
	'skr'            => 'pk',
	'sql'            => 'al',
	'szl'            => 'pl',
	'tah'            => 'pf',
	'es_EC'          => 'ec',
	'de_CH_informal' => 'ch',
	'de_DE_formal'   => 'de',
	'nl_NL_formal'   => 'nl',
);

$icons = $not_found = array();

echo '<?php', PHP_EOL, PHP_EOL;

if ( isset( $json->translations ) ) {
	$count = count( $json->translations );

	echo '/**', PHP_EOL, ' * File is auto-generated', PHP_EOL, ' * ', PHP_EOL;
	echo "* {$count} translations-teams for WordPress found", PHP_EOL, ' */', PHP_EOL;

	foreach ( $json->translations as $item ) {
		if ( isset( $exceptions[ $item->language ] ) ) {
			$icons[ $item->language ] = $exceptions[ $item->language ];
		} elseif ( 5 <= strlen( $item->language ) ) {
			$icons[ $item->language ] = strtolower( substr( $item->language, - 2 ) );
		} else {
			printf( "// Unhandled language: %s (%s)\n", $item->language, $item->english_name );
		}
	}
}

echo 'return $className = [', PHP_EOL;

foreach ( array_filter( $icons ) as $key => $value ) {
	$needle = "assets/css-flags/flags/4x3/{$value}.svg";
	$index  = array_search( $needle, $glob );
	if ( $index !== false ) {
		unset( $glob[ $index ] );
	}
	echo sprintf( "    '%s' => 'flag-icon-%s',", $key, $value ), PHP_EOL;
}

echo '];', PHP_EOL, PHP_EOL;

$count = count( $glob );
if ( $count > 0 ) {
	echo '/**', PHP_EOL, " * {$count} unused icons in assets/css-flags/flags/4x3/", PHP_EOL, ' * ', PHP_EOL;

	array_walk(
		$glob,
		function ( &$item ) {
			$item = basename( $item );
		}
	);

	foreach ( array_chunk( $glob, 14 ) as $flags ) {
		echo ' * ', implode( ', ', $flags ), PHP_EOL;
	}

	echo ' */', PHP_EOL;
}
