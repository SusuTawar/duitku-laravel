<?php

namespace SusuTawar\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SusuTawar\Types\Constant;
use SusuTawar\Types\DuitKuCallbackResponse;

class DuitKuCallback extends Controller {
  public function paymentCallback(Request $request) {
    $validator = Validator::make($request->all(), [
      'merchantCode' => 'required',
      'amount' => 'required',
      'merchantOrderId' => 'required',
      'productDetail' => 'required',
      'additionalParam' => 'nullable',
      'paymentMethod' => 'required',
      'resultCode' => 'required',
      'merchantUserId' => 'nullable',
      'reference' => 'required',
      'signature' => ['required', function (string $attribute, mixed $value, \Closure $fail) use ($request) {
        $signature = hash('md5', config('duitku.merchant_id') . $request->amount . $request->merchantOrderId . config('duitku.api_key'));
        if ($value !== $signature) $fail('Invalid ' . $attribute);
      }],
      'publisherOrderId' => 'required',
      'spUserHash' => 'required',
      'settlementDate' => 'required',
      'issuerCode' => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json($validator->errors(), 400);
    }
    $data = DuitKuCallbackResponse::create($request->all());
    if ($request->resultCode === Constant::SUCCESS) {
      $this->paymentSuccess($data);
    } else {
      $this->paymentFailed($data);
    }
    return response()->json([
        'success' => true,
        'message' => 'Request processed',
    ])->setStatusCode(200);
  }

  public function paymentSuccess(DuitKuCallbackResponse $data) {
    $handler = new (config('duitku.routing.callback_handler'));
    $handler->paymentSuccess($data);
  }

  public function paymentFailed(DuitKuCallbackResponse $data) {
    $handler = new (config('duitku.routing.callback_handler'));
    $handler->paymentFailed($data);
  }
}
