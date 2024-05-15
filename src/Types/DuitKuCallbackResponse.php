<?php

namespace SusuTawar\Types;

class DuitKuCallbackResponse
{
  public string $merchantCode;
  public ?int $amount;
  public ?string $merchantOrderId;
  public ?string $productDetail;
  public ?string $additionalParam;
  public ?string $paymentMethod;
  public ?string $resultCode;
  public ?string $merchantUserId;
  public ?string $reference;
  public ?string $signature;
  public ?string $publisherOrderId;
  public ?string $spUserHash;
  public ?string $settlementDate;
  public ?string $issuerCode;

  private function __construct() {}

  public static function create($data) {
    $instance = new self;
    $instance->merchantCode = $data['merchantCode'];
    $instance->amount = $data['amount'];
    $instance->merchantOrderId = $data['merchantOrderId'];
    $instance->productDetail = $data['productDetail'];
    $instance->additionalParam = $data['additionalParam'];
    $instance->paymentMethod = $data['paymentMethod'];
    $instance->resultCode = $data['resultCode'];
    $instance->merchantUserId = $data['merchantUserId'];
    $instance->reference = $data['reference'];
    $instance->signature = $data['signature'];
    $instance->publisherOrderId = $data['publisherOrderId'];
    $instance->spUserHash = $data['spUserHash'];
    $instance->settlementDate = $data['settlementDate'];
    $instance->issuerCode = $data['issuerCode'];
    return $instance;
  }

  public function toArray() {
    return [
      "merchantCode" => $this->merchantCode,
      "amount" => $this->amount,
      "merchantOrderId" => $this->merchantOrderId,
      "productDetail" => $this->productDetail,
      "additionalParam" => $this->additionalParam,
      "paymentMethod" => $this->paymentMethod,
      "resultCode" => $this->resultCode,
      "merchantUserId" => $this->merchantUserId,
      "reference" => $this->reference,
      "signature" => $this->signature,
      "publisherOrderId" => $this->publisherOrderId,
      "spUserHash" => $this->spUserHash,
      "settlementDate" => $this->settlementDate,
      "issuerCode" => $this->issuerCode,
    ];
  }
}
