<?php

namespace SusuTawar\Types;

class ItemDetails {
  public string $name;
  public int $quantity;
  public int $price;

  private function __construct() {}

  public static function create(mixed $name, int $quantity = null, int $price = null) {
    $item = new self();

    if (is_object($name) || is_array($name)) {
        $data = (array)$name;
        $item->name = $data['name'] ?? null;
        $item->quantity = $data['quantity'] ?? null;
        $item->price = $data['price'] ?? null;
    } else {
        $item->name = $name;
        $item->quantity = $quantity;
        $item->price = $price;
    }

    return $item;
  }

  public function toArray() {
    return [
      'name' => $this->name,
      'quantity' => $this->quantity,
      'price' => $this->price
    ];
  }
}
