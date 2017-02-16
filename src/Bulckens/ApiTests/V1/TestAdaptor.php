<?php

namespace Bulckens\ApiTests\V1;

use Bulckens\ApiTools\V1\Adaptor;

class TestAdaptor extends Adaptor {

  // Index
  public function index() {
    $this->output->add([ 'fiddl' => 'zamora' ]);
    
    return $this->render();
  }

}
