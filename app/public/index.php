<?php
require __DIR__ . '/../vendor/autoload.php';

use App\ApiService;
use App\FileCache;
use App\ShippingService;
use App\Structures\AddressItem;
use App\Structures\ProductItem;
use GuzzleHttp\Exception\GuzzleException;

$api = new ApiService( '77qn9aax-qrrm-idki:lnh0-fm2nhmp0yca7' );

// multiple order items can be entered
$orderItems = new ProductItem( [
	[ 'variant_id' => 7679, 'quantity' => 2 ]
] );

$address = new AddressItem(
	'11025 Westlake Dr, Charlotte, North Carolina, 28273',
	'US'
);

$cache   = new FileCache( 'cache' );
$service = new ShippingService( $api, $cache );

try {
	$rates = $service->getRates( $orderItems, $address );
} catch ( GuzzleException $e ) {
	print_r( $e->getMessage() );
}

print_r( $rates );
