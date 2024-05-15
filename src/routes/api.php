<?php

use Illuminate\Support\Facades\Route;
use SusuTawar\Controllers\DuitKuCallback;

if (config('duitku.routing.enabled')) {
  $routeConfig = [
    'middleware' => ['api'],
  ];

  app('router')->group($routeConfig, function ($router) {
    $router->post(config('duitku.routing.custom_route') ?? '/duitku/callback', [DuitKuCallback::class, 'paymentCallback'])->name('duitku.callback');
  });
}
