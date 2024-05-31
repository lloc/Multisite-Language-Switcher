<?php

namespace lloc\Msls;

class MslsFields {

	const FIELD_BLOG_ID        = 'blog_id';
	const FIELD_POST_TYPE      = 'post_type';
	const FIELD_TAXONOMY       = 'taxonomy';
	const FIELD_S              = 's';
	const FIELD_ACTION         = 'action';
	const FIELD_MSLS_FILTER    = 'msls_filter';
	const FIELD_MSLS_NONCENAME = 'msls_noncename';
	const FIELD_MSLS_ID        = 'msls_id';
	const FIELD_MSLS_LANG      = 'msls_lang';

	const CONFIG = array(
		self::FIELD_BLOG_ID        => array(
			INPUT_POST,
			FILTER_SANITIZE_NUMBER_INT,
		),
		self::FIELD_POST_TYPE      => array(
			INPUT_POST,
			FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		),
		self::FIELD_TAXONOMY       => array(
			INPUT_POST,
			FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		),
		self::FIELD_S              => array(
			INPUT_POST,
			FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		),
		self::FIELD_ACTION         => array(
			INPUT_POST,
			FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		),
		self::FIELD_MSLS_FILTER    => array(
			INPUT_GET,
			FILTER_SANITIZE_NUMBER_INT,
		),
		self::FIELD_MSLS_NONCENAME => array(
			INPUT_POST,
			FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		),
		self::FIELD_MSLS_ID        => array(
			INPUT_GET,
			FILTER_SANITIZE_NUMBER_INT,
		),
		self::FIELD_MSLS_LANG      => array(
			INPUT_GET,
			FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		),
	);
}
