<?php

namespace SusuTawar\Types;


class PaymentMethod {
  public $method;
  public $name;
  public $image;
  public $fee;

  private function __construct() {}

  public static function create($method, $name = null, $image = null, $fee = null) {
    $instance = new self();

    if (is_object($method) || is_array($method)) {
        $data = (array)$method;
        $instance->method = $data['paymentMethod'] ?? null;
        $instance->name = $data['paymentName'] ?? null;
        $instance->image = $data['paymentImage'] ?? null;
        $instance->fee = $data['totalFee'] ?? null;
    } else {
        $instance->method = $method;
        $instance->name = $name;
        $instance->image = $image;
        $instance->fee = $fee;
    }

    return $instance;
  }
}
