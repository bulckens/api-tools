<?php

namespace spec\Bulckens\ApiTools;

use Bulckens\ApiTools\Sort;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SortSpec extends ObjectBehavior {

  function let() {
    $this->beConstructedWith( 'fairy-desc' );
  }
  

  // Key method
  function it_returns_the_sort_order_key() {
    $this->key()->shouldBe( 'fairy' );
  }

  function it_returns_the_fallback_sort_order_key_when_none_is_given() {
    $this->beConstructedWith( null );
    $this->key( 'id' )->shouldBe( 'id' );
  }

  function it_does_not_return_the_fallback_sort_order_key_when_one_is_given() {
    $this->beConstructedWith( 'created_at-desc' );
    $this->key( 'id' )->shouldBe( 'created_at' );
  }


  // Way method
  function it_returns_the_sort_order_direction() {
    $this->way()->shouldBe( 'desc' );
  }

  function it_returns_the_default_sort_order_direction() {
    $this->beConstructedWith( 'field' );
    $this->way()->shouldBe( 'asc' );
  }

  function it_returns_the_fallback_sort_order_direction() {
    $this->beConstructedWith( 'column' );
    $this->way( 'desc' )->shouldBe( 'desc' );
  }

  function it_returns_the_given_sort_order_direction() {
    $this->beConstructedWith( 'name-asc' );
    $this->way( 'desc' )->shouldBe( 'asc' );
  }

}
