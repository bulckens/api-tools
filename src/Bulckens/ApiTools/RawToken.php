<?php

namespace Bulckens\ApiTools;

class RawToken extends Token {

  // Fetch secret
  protected function fetchSecret( $secret ) {
    return $secret;
  }

}
