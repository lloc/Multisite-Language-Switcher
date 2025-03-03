<?php declare(strict_types=1);

require_once __DIR__ . '/../phpunit/WP_CLI.php';

class_alias( \lloc\MslsTests\WP_CLI::class, '\WP_CLI' );

function is_woocommerce(): bool {
	return class_exists( 'WooCommerce' );
}
