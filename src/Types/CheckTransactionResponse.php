<?php

namespace SusuTawar\Types;

class CheckTransactionResponse
{
  public string $merchantOrderId;
  public string $reference;
  public string $status;
  public string $statusStr;
  public int $amount;

  public static function create($merchantOrderId, string $reference = null, int $amount = null, string $statusCode = null): self
  {
    $instance = new self();

    if (is_object($merchantOrderId) || is_array($merchantOrderId)) {
      $data = (array)$merchantOrderId;
      $instance->merchantOrderId = $data['merchantOrderId'] ?? null;
      $instance->reference = $data['reference'] ?? null;
      $instance->amount = $data['amount'] ?? null;
      $instance->status = $data['statusCode'];
    } else {
      $instance->merchantOrderId = $merchantOrderId;
      $instance->reference = $reference;
      $instance->amount = $amount;
      $instance->status = $statusCode;
    }
    switch ($instance->status) {
      case Constant::SUCCESS:
        $instance->statusStr = 'Success';
        break;
      case Constant::PENDING:
        $instance->statusStr = 'Pending';
        break;
      case Constant::FAILED:
        $instance->statusStr = 'Failed';
        break;
      default:
        $instance->statusStr = 'Unknown';
    }

    return $instance;
  }
}
