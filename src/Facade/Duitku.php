<?php

namespace SusuTawar\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Duitku Facade
 *
 * @method static \Dekasender\HttpClient\Response sendEmail(string $receiver, string $subject, string $message, string $sender, string $replyTo)
 * @method static Collection|\SusuTawar\Types\PaymentMethod[] getPaymentMethods(int $amount) Retrieves the available payment methods for a given amount.
 * @method static \SusuTawar\Types\InquiryResponse makeTransaction(int $amount, string $orderId, string $productDetail, int $customerEmail, string|PaymentMethod $method, string $customerName, int $expiry, Collection|ItemDetails[] $itemDetails = collect(), string $customerPhone = null, string $merchantUserInfo = null) Makes a transaction with DuitKu.
 * @method static \SusuTawar\Types\CheckTransactionResponse checkTransaction(string $orderId)Checks the status of a transaction.
 */
class Duitku extends Facade
{
  protected static function getFacadeAccessor()
  {
    return 'susutawar.duitku';
  }
}
