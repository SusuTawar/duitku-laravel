<?php

namespace SusuTawar\Interfaces;

use SusuTawar\Types\DuitKuCallbackResponse;

interface DuitKuCallbackInterface {
  public function paymentSuccess(DuitKuCallbackResponse $data);
  public function paymentFailed(DuitKuCallbackResponse $data);
}
