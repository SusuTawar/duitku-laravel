<?php

namespace SusuTawar\Types;

class InquiryResponse {
  public string $reference;
  public ?string $paymentUrl;
  public ?string $vaNumber;
  public ?string $qrString;
  public int $amount;

  private function __construct() {}

  public static function create(mixed $reference, int $amount = null, ?string $paymentUrl = null, ?string $vaNumber = null, ?string $qrString = null) {
    $instance = new self();

    if (is_object($reference) || is_array($reference)) {
        $data = (array)$reference;
        $instance->reference = $data['reference'] ?? null;
        $instance->paymentUrl = $data['paymentUrl'] ?? null;
        $instance->vaNumber = $data['vaNumber'] ?? null;
        $instance->qrString = $data['qrString'] ?? null;
        $instance->amount = $data['amount'] ?? null;
    } else {
        $instance->reference = $reference;
        $instance->paymentUrl = $paymentUrl;
        $instance->vaNumber = $vaNumber;
        $instance->qrString = $qrString;
        $instance->amount = $amount;
    }

    return $instance;
  }
}
