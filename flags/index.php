<?php

/**
 * Reads a previously with wget retrieved file from wordpress.org
 *
 * http://api.wordpress.org/translations/core/1.0/
 *
 * and transforms it into a hashmap with language as key and the name
 * of the flag-icon as value.
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

$content = file_get_contents( 'index.html' );
$json    = json_decode( $content );
$glob    = glob( '*.png' );

$exceptions = [
	'el'  => 'gr.png',
	'et'  => 'ee.png',
	'ja'  => 'jp.png',
	'uk'  => 'ua.png',
	'ca'  => 'catalonia.png',
	'eo'  => 'europeanunion.png',
	'cy'  => 'wales.png',
	'gd'  => 'scotland.png',
	'af'  => 'za.png',
	'ar'  => 'arableague.png',
	'ary' => 'ma.png',
	'as'  => 'in.png',
	'az'  => 'az.png',
	'azb' => 'az.png',
	'bo'  => 'cn.png',
	'bel' => 'by.png',
	'ceb' => 'ph.png',
	'dzo' => 'bt.png',
	'eu'  => 'es.png',
	'fi'  => 'fi.png',
	'fur' => 'it.png',
	'gu'  => 'in.png',
	'haz' => 'af.png',
	'hr'  => 'hr.png',
	'hy'  => 'am.png',
	'kab' => 'dz.png',
	'kk'  => 'kz.png',
	'km'  => 'kh.png',
	'ckb' => 'iq.png',
	'lo'  => 'la.png',
	'lv'  => 'lv.png',
	'mn'  => 'mn.png',
	'mr'  => 'in.png',
	'oci' => 'catalonia.png',
	'ps'  => 'af.png',
	'rhg' => '', // Rohingya
	'sq'  => 'al.png',
	'sah' => 'ru.png',
	'sql' => 'al.png',
	'szl' => 'pl.png',
	'te'  => 'in.png',
	'th'  => 'th.png',
	'tl'  => 'ph.png',
	'tah' => 'pf.png',
	'ur'  => 'pk.png',
	'vi'  => 'vn.png',
];

$icons = $not_found = [];

if ( isset( $json->translations ) ) {
	foreach ( $json->translations as $item ) {
		if ( isset( $exceptions[ $item->language ] ) ) {
			$icons[ $item->language ] = $exceptions[ $item->language ];
		} elseif ( 5 <= strlen( $item->language ) ) {
			$icons[ $item->language ] = strtolower( substr( $item->language, 3, 2 ) ) . '.png';
		} else {
			printf( "Unhandled language: %s (%s)\n", $item->language, $item->english_name );
		}
	}
}

echo json_encode( array_filter ( $icons ) );
