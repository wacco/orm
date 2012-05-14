<?php

namespace ORM\Types;

/**
 * Objekt reprezentujuci cenu
 * @author Branislav Vaculčiak
 */
class Price implements IType {
	
	const EUR = '€';

	protected $price = 0;
	protected $tax = 20;
	protected $count = 1;

	public function __construct($price, $tax, $currency = self::EUR) {
		$this->price = $price;
		$this->tax = $tax;
		$this->currency = $currency;
	}

	public function multiply($count) {
		$this->count = $count;
		return $this;
	}

	public function getPrice() {
		return $this->getPriceWithTax();
	}

	public function getPriceWithTax() {
		return $this->getPriceWithoutTax() * ($this->getTax() ? ($this->getTax() / 100 + 1) : 1);
	}

	public function getPriceWithoutTax() {
		return $this->price * $this->count;
	}

	public function getTax() {
		return $this->tax;
	}

	public function getCurrency() {
		return $this->currency;
	}

	public function formatPriceWithTax() {
		return $this->format($this->getPriceWithTax());
	}

	public function formatPriceWithoutTax() {
		return $this->format($this->getPriceWithoutTax());
	}

	public function formatTax() {
		return sprintf("%d %%", $this->getTax());
	}

	protected function format($price) {
		return number_format($price, 2, ',', ' ') . ' ' . $this->getCurrency();
	}

	public function __toString() {
		return $this->format($this->getPriceWithTax());
	}
}