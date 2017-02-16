<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\V1\Api;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApiSpec extends ObjectBehavior {

  function let() {
    new App( 'dev', __DIR__, 4 );
    $this->beConstructedWith( 'fake', 'generic' );
  }


  // Initialization
  function it_creates_a_new_request_with_the_given_source() {
    $this->request()->source()->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_creates_a_new_request_with_the_default_secret() {
    $this->beConstructedWith( 'fake' );
    $this->request()->secret()->shouldBe( '1234567891011121314151617181920212223242526272829303132333435363' );
  }

  function it_creates_a_new_request_with_the_given_secret() {
    $this->beConstructedWith( 'fake', 'reverse' );
    $this->request()->secret()->shouldBe( '3635343332313039282726252423222120291817161514131211101987654321' );
  }

  
  // Request method
  function it_returns_the_request_instance() {
    $this->request()->shouldHaveType( 'Bulckens\\ApiTools\\V1\\Request' );
  }

}
