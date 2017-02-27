<?php

namespace spec\Bulckens\ApiTools;

use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\Sort;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SortSpec extends ObjectBehavior {

  function let() {
    Api::get()->file( 'api.yml' );
    $this->beConstructedWith( 'fairy-desc' );
  }


  // Initialization
  function it_accepts_a_delimited_string() {
    $this->beConstructedWith( 'aap-desc' );
    $this->key()->shouldBe( 'aap' );
    $this->way()->shouldBe( 'desc' );
  }

  function it_accepts_a_key_value_pair() {
    $this->beConstructedWith( 'noot', 'desc' );
    $this->key()->shouldBe( 'noot' );
    $this->way()->shouldBe( 'desc' );
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

  function it_returns_the_sort_order_key_with_custom_delimiter() {
    Api::get()->file( 'api.delimiter.yml' );
    $this->beConstructedWith( 'updated_at#desc' );
    $this->key( 'id' )->shouldBe( 'updated_at' );
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

  function it_returns_the_sort_order_direction_with_custom_delimiter() {
    Api::get()->file( 'api.delimiter.yml' );
    $this->beConstructedWith( 'name#asc' );
    $this->way()->shouldBe( 'asc' );
  }

  function it_returns_the_configured_sort_order_direction() {
    Api::get()->file( 'api.sort_order.yml' );
    $this->beConstructedWith( 'field' );
    $this->way()->shouldBe( 'desc' );
  }



  // Get method
  function it_generates_a_sort_order_key_from_an_instance() {
    $this->get()->shouldBe( 'fairy-desc' );
  }



  // Asc method
  function it_tests_positive_for_ascending_order() {
    $this->beConstructedWith( 'fairy-asc' );
    $this->asc()->shouldBe( true );
  }

  function it_tests_negative_for_ascending_order_when_fallback_is_descending() {
    $this->beConstructedWith( 'fairy' );
    $this->asc( 'desc' )->shouldBe( false );
  }


  // Desc method
  function it_tests_positive_for_descending_order() {
    $this->beConstructedWith( 'fairy-desc' );
    $this->desc()->shouldBe( true );
  }

  function it_tests_negative_for_descending_order_when_fallback_is_ascending() {
    $this->beConstructedWith( 'fairy' );
    $this->desc( 'asc' )->shouldBe( false );
  }


  // Delimieter method (static)
  function it_returns_the_default_delimiter() {
    $this::delimiter()->shouldBe( '-' );
  }

  function it_returns_the_custom_delimiter() {
    Api::get()->file( 'api.delimiter.yml' );
    $this::delimiter()->shouldBe( '#' );
  }


  // Order method (static)
  function it_generates_a_sort_order_key() {
    $this::order( 'id', 'desc' )->shouldBe( 'id-desc' );
  }

  function it_generates_a_sort_order_key_with_custom_delimiter() {
    Api::get()->file( 'api.delimiter.yml' );
    $this::order( 'id', 'desc' )->shouldBe( 'id#desc' );
  }

}
