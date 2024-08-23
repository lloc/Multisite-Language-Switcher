<?php declare(strict_types=1);

function is_woocommerce(): bool {
	return class_exists( 'WooCommerce' );
}
