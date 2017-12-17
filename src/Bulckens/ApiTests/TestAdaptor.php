<?php

namespace Bulckens\ApiTests;

use Bulckens\ApiTools\Adaptor;

class TestAdaptor extends Adaptor {

  protected $before_action;
  protected $after_action;

  // Before
  public function before( $action ) {
    $this->before_action = $action;
  }


  // After
  public function after( $action ) {
    $this->after_action = $action;
  }


  // Index
  public function index() {
    $this->output->add([ 'fiddl' => 'zamora' ]);
    return $this->render();
  }


  // Test if before is called
  public function beforeAction() {
    return $this->before_action;
  }


  // Test if after is called
  public function afterAction() {
    return $this->after_action;
  }

}
