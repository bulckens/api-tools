<?php

namespace Bulckens\ApiTests;

use Bulckens\ApiTools\Adaptor;

class TestAdaptor extends Adaptor {

  // Index
  public function index() {
    $this->output->add([ 'fiddl' => 'zamora' ]);
    
    return $this->render();
  }

}
