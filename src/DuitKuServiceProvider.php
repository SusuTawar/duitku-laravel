<?php

namespace SusuTawar;

use Illuminate\Support\ServiceProvider;

class DuitKuServiceProvider extends ServiceProvider {
  public function boot()
  {
    $this->publishes([
      __DIR__ . '/config/duitku.php' => config_path('duitku.php'),
    ], ['duitku']);
    $this->loadRoutesFrom(realpath(__DIR__.'/routes/api.php'));
  }

  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__ . '/config/duitku.php',
      'duitku'
    );
    $this->app->bind('susutawar.duitku', function () {
      return new DuitKuProcess();
    });
  }
}
