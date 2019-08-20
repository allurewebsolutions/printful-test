<?php

namespace App\Structures;

class AddressItem {
	/**
	 * @var string
	 */
	private $address;

	/**
	 * @var string
	 */
	private $countryCode;

	/**
	 * AddressItem constructor.
	 *
	 * @param string $shippingAddress
	 * @param string $countryCode
	 */
	public function __construct( string $shippingAddress, string $countryCode ) {
		$this->address     = $shippingAddress;
		$this->countryCode = $countryCode;

	}

	/**
	 * @return string
	 */
	public function getShippingAddress() {
		return $this->address;
	}

	/**
	 * @return string
	 */
	public function getCountryCode() {
		return $this->countryCode;
	}
}
