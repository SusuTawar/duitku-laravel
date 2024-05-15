<?php

return [
  /**
   * Account configuration
   *
   * set sandbox mode to false for production
   */
  "merchant_id" => env('DUITKU_MERCHANT_ID'),
  "api_key" => env('DUITKU_API_KEY'),
  "sandbox_mode" => env('DUITKU_SANDBOX_MODE', true),

  /**
   * Let the package handle the routing for callbacks.
   * The registered route will be under the `api` group to avoid CSRF.
   *
   * To use this feature, create a class that implements the SusuTawar\Interfaces\DuitKuCallbackInterface.
   * For example, `class MyHandler implements DuitKuCallbackInterface`.
   * Then, specify the class name in the callback_handler.
   * For example, `"callback_handler" => MyHandler::class`.
   *
   * If this feature is disabled, you will need to define the route yourself.
   */
  "routing" => [
    "enabled" => true,
    "custom_route" => null,
    "callback_handler" => null,
  ],

  /**
   * This section pertains to the Callback and Return URL.
   * The values can either be a registered route name or a URL.
   *
   * Please note that the callback_url will not be utilized when routing config is enabled.
   *
   * You can extend or refer to the \SusuTawar\Controllers\DuitKuCallback to get started.
   * Remember to disable CSRF protection.
   */
  "return_url" => env('DUITKU_RETURN_URL'),
  "callback_url" => env('DUITKU_CALLBACK_URL'),
];
