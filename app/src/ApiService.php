<?php

namespace App;

use App\Structures\AddressItem;
use App\Structures\ProductItem;
use GuzzleHttp\Client;

/**
 * Simple Printful API call wrapper
 */
class ApiService {
	/**
	 * @var string
	 */
	private $apiKey;

	/**
	 * ApiService constructor.
	 *
	 * @param string $apiKey
	 */
	public function __construct( string $apiKey ) {
		$this->apiKey = $apiKey;
	}

	/**
	 * @param ProductItem $products
	 * @param AddressItem $address
	 *
	 * @return string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getRates( ProductItem $products, AddressItem $address ) {
		$body = [
			'recipient' => [
				'address1'     => $address->getShippingAddress(),
				'country_code' => $address->getCountryCode()
			],
			'items'     => $products->getOrderItems(),
		];

		// make request to Printful API
		$res = ( new Client )->request( 'POST', 'https://api.printful.com/shipping/rates', [
			'headers' => [
				'authorization' => 'Basic ' . base64_encode( $this->apiKey ),
			],
			'body'    => json_encode( $body ),
		] );

		return $res->getBody()->getContents();
	}
}
