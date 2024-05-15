<?php

namespace SusuTawar;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use SusuTawar\Exceptions\DuitKuAuthException;
use SusuTawar\Exceptions\DuitKuBadRequestException;
use SusuTawar\Exceptions\DuitKuNotFoundException;
use SusuTawar\Exceptions\DuitKuPaymentException;
use SusuTawar\Types\CheckTransactionResponse;
use SusuTawar\Types\InquiryResponse;
use SusuTawar\Types\ItemDetails;
use SusuTawar\Types\PaymentMethod;

class DuitKuProcess {
  private $merchantId;
  private $apiKey;
  private $sandbox;

  public function __construct() {
    $this->merchantId = config('duitku.merchant_id');
    $this->apiKey = config('duitku.api_key');
    $this->sandbox = config('duitku.sandbox_mode');
  }

  private function url() {
    if($this->sandbox)
      return "https://sandbox.duitku.com/webapi/api";
    return "https://passport.duitku.com/webapi/api";
  }

  private function throwUnsuccessful(Response $response) {
    $body = $response->json();
    $message = isset($body['statusMessage']) ? $body['statusMessage'] : null;
    if (!$message) {
      $message = isset($body['Message']) ? $body['Message'] : "Something went wrong";
    }
    switch ((int)$response->status()) {
      case 400:
        throw new DuitKuBadRequestException($message);
      case 401:
      case 403:
        throw new DuitKuAuthException($message);
      case 404:
        throw new DuitKuNotFoundException($message);
      case 409:
        throw new DuitKuPaymentException($message);
      default:
        throw new \Exception($message);
    }
  }

/**
 * Get Payment Methods
 *
 * This method retrieves the available payment methods for a given amount.
 *
 * @see https://docs.duitku.com/api/id/#get-payment-method
 *
 * @param int $amount The amount for which payment methods are to be retrieved.
 *
 * @return Collection|PaymentMethod[] A collection of PaymentMethod objects representing the available payment methods.
 *
 * @throws \SusuTawar\Exceptions\DuitKuBadRequestException When the request parameter are not sent correctly.
 * @throws \SusuTawar\Exceptions\DuitKuAuthException When the request are not properly authenticated, ie. bad signature.
 * @throws \SusuTawar\Exceptions\DuitKuNotFoundException When the request have an unprocessable value. ie. bad merchant_id.
 * @throws \SusuTawar\Exceptions\DuitKuPaymentException When the payment cannot be processed.
 * @throws \Exception If the request to the DuitKu API fails or if the response status code is not '00'.
 * @see https://docs.duitku.com/api/id/#http-code
 */
  public function getPaymentMethods($amount) {
    $dateTime = Carbon::now()->format('Y-m-d H:i:s');
    $response = Http::asJson()
      ->acceptJson()
      ->post($this->url() . '/merchant/paymentmethod/getpaymentmethod', [
        "merchantcode" => $this->merchantId,
        "amount" => (int)$amount,
        "datetime" => $dateTime,
        "signature" => hash('sha256', $this->merchantId . $amount . $dateTime . $this->apiKey),
      ]);
    if (!$response->successful()) {
      return $this->throwUnsuccessful($response);
    }
    $body = $response->json();
    if ($body['responseCode'] !== '00') {
      throw new \Exception($body['responseMessage']);
    }
    return collect($body['paymentFee'])->map(fn ($v) => PaymentMethod::create($v));
  }

  /**
 * Make a transaction with DuitKu.
 *
 * @see https://docs.duitku.com/api/id/#permintaan-transaksi
 *
 * @param int $amount The amount of the transaction.
 * @param string $orderId The unique identifier for the transaction.
 * @param string $productDetail The details of the product being purchased.
 * @param string $customerEmail The email address of the customer.
 * @param string|PaymentMethod $method The payment method to be used @see getPaymentMethods.
 * @param string $customerName The name of the customer.
 * @param ?int $expiry The expiry period of the transaction (in Minutes), set to null for default expiry time.
 * @param Collection|ItemDetails[] $itemDetails An optional collection of item detail.
 * @param string $customerPhone The phone number of the customer.
 * @param string $merchantUserInfo User's name within merchant's system.
 *
 * @return InquiryResponse The response from DuitKu containing the transaction details.
 *
 * @throws \SusuTawar\Exceptions\DuitKuBadRequestException When the request parameter are not sent correctly.
 * @throws \SusuTawar\Exceptions\DuitKuAuthException When the request are not properly authenticated, ie. bad signature.
 * @throws \SusuTawar\Exceptions\DuitKuNotFoundException When the request have an unprocessable value. ie. bad merchant_id.
 * @throws \SusuTawar\Exceptions\DuitKuPaymentException When the payment cannot be processed.
 * @throws \Exception If the request to the DuitKu API fails or if the response status code is not '00'.
 * @see https://docs.duitku.com/api/id/#http-code
 */
  public function makeTransaction(
    int $amount,
    string $orderId,
    string $productDetail,
    string $customerEmail,
    string|PaymentMethod $method,
    string $customerName,
    ?int $expiry,
    Collection $itemDetails = null,
    string $customerPhone = null,
    string $merchantUserInfo = null,
  ) {
    $signature = md5($this->merchantId . $orderId . $amount . $this->apiKey);
    $appUrl = config('app.url');
    if (!$itemDetails) $itemDetails = collect();
    $returnUrl = ltrim(
      Route::has(config('duitku.return_url')) ? route(config('duitku.return_url')) : config('duitku.return_url'),
      '/'
    );
    if (!str_starts_with($returnUrl, 'http')) {
      $returnUrl = "$appUrl/" . ltrim($returnUrl, '/');
    }
    if (!config('duitku.routing.enabled')) {
      $callbackUrl = ltrim(
        Route::has(config('duitku.callback_url')) ? route(config('duitku.callback_url')) : config('duitku.callback_url'),
        '/'
      );
      if (!str_starts_with($callbackUrl, 'http')) {
        $callbackUrl = "$appUrl/" . ltrim($callbackUrl, '/');
      }
    } else {
      $callbackUrl = route('duitku.callback');
    }
    $requestParam = [
      "merchantCode" => $this->merchantId,
      "paymentAmount" => $amount,
      "merchantOrderId" => $orderId,
      "productDetails" => $productDetail,
      "email" => $customerEmail,
      "customerVaName" => $customerName,
      "paymentMethod" => $method,
      "returnUrl" => $returnUrl,
      "callbackUrl" => $callbackUrl,
      "signature" => $signature,
      "expiryPeriod" => $expiry,
      "phoneNumber" => $customerPhone,
      "merchantUserInfo" => $merchantUserInfo,
      "itemDetails" => $itemDetails->map(fn ($v) => $v->toArray())->toArray(),
    ];
    $requestParam = array_filter($requestParam, fn ($v) =>!is_null($v));
    $response = Http::asJson()
      ->acceptJson()
      ->post($this->url() . '/merchant/v2/inquiry', $requestParam);
    if (!$response->successful()) {
      return $this->throwUnsuccessful($response);
    }
    $body = $response->json();
    return InquiryResponse::create($body);
  }

  /**
   * Check the status of a transaction.
   *
   * This method checks the status of a transaction using the provided order ID.
   *
   * @param string $orderId The unique identifier for the transaction.
   *
   * @return CheckTransactionResponse The response from DuitKu containing the transaction details.
   *
   * @throws \SusuTawar\Exceptions\DuitKuBadRequestException When the request parameter are not sent correctly.
   * @throws \SusuTawar\Exceptions\DuitKuAuthException When the request are not properly authenticated, ie. bad signature.
   * @throws \SusuTawar\Exceptions\DuitKuNotFoundException When the request have an unprocessable value. ie. bad merchant_id.
   * @throws \SusuTawar\Exceptions\DuitKuPaymentException When the payment cannot be processed.
   * @throws \Exception If the request to the DuitKu API fails or if the response status code is not '00'.
   * @see https://docs.duitku.com/api/id/#http-code
   */
  public function checkTransaction($orderId) {
    $signature = md5($this->merchantId . $orderId .$this->apiKey);
    $response = Http::asForm()
      ->acceptJson()
      ->post($this->url() . '/merchant/transactionStatus', [
        'merchantCode' => $this->merchantId,
        'orderId' => $orderId,
        'signature' => $signature,
      ]);
    if (!$response->successful()) {
      return $this->throwUnsuccessful($response);
    }
    $body = $response->json();
    if ($body['statusCode'] === '02') {
      throw new \Exception($body['statusMessage']);
    }
    return CheckTransactionResponse::create($body);
  }
}
