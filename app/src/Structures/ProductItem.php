<?php

namespace App\Structures;

class ProductItem {
	/**
	 * @var array
	 */
	private $products;

	/**
	 * ProductItem constructor.
	 *
	 * @param array $products
	 */
	public function __construct( array $products ) {
		$this->products = $products;
	}

	/**
	 * Get formatted order items for usage in API
	 *
	 * @return array
	 */
	public function getOrderItems() {
		$itemArr = [];

		foreach ( $this->products as $product ) {
			array_push( $itemArr,
				[
					'variant_id' => $product['variant_id'],
					'quantity'   => $product['quantity']
				]
			);
		}

		return $itemArr;
	}
}
