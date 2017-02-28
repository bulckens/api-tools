<?php

namespace spec\Bulckens\ApiTools;

use Bulckens\ApiTests\TestAdaptor;
use Bulckens\ApiTools\Action;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ActionSpec extends ObjectBehavior {

  protected $adaptor;
  
  function let() {
    $this->adaptor = new TestAdaptor();
    $this->beConstructedWith( 'index', $this->adaptor );
  }


  // Tested through TestAdaptorSpec.php

}
