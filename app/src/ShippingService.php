<?php

namespace App;

use App\Interfaces\CacheInterface;
use App\Structures\AddressItem;
use App\Structures\ProductItem;

class ShippingService {
	/**
	 * @var \App\ApiService
	 */
	private $api;
	/**
	 * @var \App\Interfaces\CacheInterface
	 */
	private $cache;

	public function __construct( ApiService $api, CacheInterface $cache ) {
		$this->api   = $api;
		$this->cache = $cache;
	}

	/**
	 * @param ProductItem $products
	 * @param AddressItem $address
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getRates( ProductItem $products, AddressItem $address ): array {
		$cachedShippingRates = $this->cache->get( 'shipping_rates' );
		$orderItems          = $products->getOrderItems();

		// if there is no cache or cache has expired or current order items don't equal cached order items
		if ( ! $cachedShippingRates || ( $cachedShippingRates['items'] !== $orderItems ) ) {
			$rates = json_decode( $this->api->getRates( $products, $address ) )->result;

			$cacheObject = [
				'rates' => $rates,
				'items' => $orderItems
			];

			// set cache for 5 minutes
			$this->cache->set( 'shipping_rates', $cacheObject, .1 * 60 );

			echo 'without cache:';

			return $rates;
		}

		echo 'with cache:';

		return $cachedShippingRates['rates'];
	}
}
