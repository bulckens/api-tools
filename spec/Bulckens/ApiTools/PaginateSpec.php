<?php

namespace spec\Bulckens\ApiTools;

use Illuminate\Database\Eloquent\Collection;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\Paginate;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PaginateSpec extends ObjectBehavior {

  function let() {
    $this->beConstructedWith( $this->create_collection( 25 ));
  }

  
  // Construct method (magic)
  function it_accepts_a_numeric_value_as_the_first_parameter() {
    $this->beConstructedWith( 16 );
    $this->total->shouldBe( 16 );
  }

  function it_accepts_an_array_as_the_first_parameter() {
    $this->beConstructedWith([ 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' ]);
    $this->total->shouldBe( 8 );
  }

  function it_accepts_an_eloquent_collection_as_the_first_parameter(){
    $this->beConstructedWith( $this->create_collection( 5 ));
    $this->total->shouldBe( 5 );
  }

  function it_fails_if_no_acceptable_value_is_provided_as_the_first_parameter() {
    $this->shouldThrow( 'Bulckens\ApiTools\PaginateItemsNotAcceptableException' )->during__Construct( 'abc' );
  }

  function it_accepts_a_pagination_number_as_the_second_argument() {
    $this->beConstructedWith([], 6 );
    $this->per_page->shouldBe( 6 );
  }

  function it_accepts_an_options_array_as_the_second_argument() {
    $this->beConstructedWith([], [
      'page' => 8
    , 'per_page' => 12
    ]);
    $this->per_page->shouldBe( 12 );
    $this->page->shouldBe( 8 );
  }


  // Total property
  function it_returns_total_number_of_items() {
    $this->beConstructedWith( $this->create_collection( 52 ));
    $this->total->shouldBe( 52 );
  }


  // Page property
  function it_returns_the_current_page() {
    $this->beConstructedWith( $this->create_collection( 21 ), [
      'per_page' => 5
    , 'page' => 2
    ]);
    $this->page->shouldBe( 2 );
  }

  function it_defaults_to_the_first_page_if_none_is_provided() {
    $this->beConstructedWith( $this->create_collection( 21 ));
    $this->page->shouldBe( 1 );
  }


  // Per page property
  function it_returns_the_given_per_page_option() {
    $this->beConstructedWith( $this->create_collection( 21 ), [
      'per_page' => 5
    ]);
    $this->per_page->shouldBe( 5 );
  }

  function it_returns_the_default_per_page_value_if_none_is_given() {
    $this->per_page->shouldBe( 15 );
  }


  // Pages property
  function it_returns_the_total_number_of_pages() {
    $this->beConstructedWith( $this->create_collection( 21 ), [
      'per_page' => 5
    ]);
    $this->pages->shouldBe( 5 );
  }

  function it_returns_one_page_if_the_collection_is_too_short() {
    $this->beConstructedWith( $this->create_collection( 4 ), [
      'per_page' => 5
    ]);
    $this->pages->shouldBe( 1 );
  }

  function it_returns_zero_pages_if_the_collection_is_empty() {
    $this->beConstructedWith([]);
    $this->pages->shouldBe( 0 );
  }


  // Next property
  function it_returns_the_next_page_number() {
    $this->beConstructedWith( $this->create_collection( 21 ), [
      'per_page' => 5
    , 'page' => 2
    ]);
    $this->next->shouldBe( 3 );
  }

  function it_returns_nothing_if_there_is_no_next_page() {
    $this->beConstructedWith( $this->create_collection( 21 ), [
      'per_page' => 5
    , 'page' => 5
    ]);
    $this->next->shouldBe( null );
  }


  // Previous property
  function it_returns_the_previous_page_number() {
    $this->beConstructedWith( $this->create_collection( 21 ), [
      'per_page' => 5
    , 'page' => 2
    ]);
    $this->previous->shouldBe( 1 );
  }

  function it_returns_nothing_if_there_is_no_previous_page() {
    $this->beConstructedWith( $this->create_collection( 21 ), [
      'per_page' => 5
    , 'page' => 1
    ]);
    $this->previous->shouldBe( null );
  }


  // Range property
  function it_returns_the_current_range() {
    $this->beConstructedWith( $this->create_collection( 55 ), [
      'per_page' => 10
    , 'page' => 2
    ]);
    $this->range->shouldBeArray();
    $this->range->shouldHaveKeyWithValue( 0, 11 );
    $this->range->shouldHaveKeyWithValue( 1, 20 );
    $this->range->shouldHaveKeyWithValue( 'start', 11 );
    $this->range->shouldHaveKeyWithValue( 'end', 20 );
  }


  // Required property
  function it_is_necessary_is_there_are_many_pages() {
    $this->beConstructedWith( $this->create_collection( 21 ), [
      'per_page' => 4
    ]);
    $this->necessary->shouldBe( true );
  }

  function it_is_not_necessary_if_there_is_only_one_page() {
    $this->beConstructedWith( $this->create_collection( 5 ), [
      'per_page' => 6
    ]);
    $this->necessary->shouldBe( false );
  }

  function it_is_not_necessary_if_the_collection_is_empty() {
    $this->beConstructedWith([]);
    $this->necessary->shouldBe( false );
  }


  // ToArray method
  function it_converts_to_an_array() {
    $this->beConstructedWith( $this->create_collection( 53 ), [
      'per_page' => 9
    , 'page' => 2
    ]);
    $array = $this->toArray();
    $array->shouldHaveKeyWithValue( 'per_page', 9 );
    $array->shouldHaveKeyWithValue( 'page', 2 );
    $array->shouldHaveKeyWithValue( 'next', 3 );
    $array->shouldHaveKeyWithValue( 'previous', 1 );
    $array->shouldHaveKeyWithValue( 'necessary', true );
    $array->shouldHaveKeyWithValue( 'total', 53 );
    $array->shouldHaveKey( 'range' );
    $array['range']->shouldHaveKeyWithValue( 0, 10 );
    $array['range']->shouldHaveKeyWithValue( 1, 18 );
    $array['range']->shouldHaveKeyWithValue( 'start', 10 );
    $array['range']->shouldHaveKeyWithValue( 'end', 18 );
  }


  // Collection builder
  function create_collection( $number ) {
    $collection = new Collection();

    for ( $i = 0; $i < $number ; $i++ ) { 
      $collection->add([ 'bla' => "lala_$i" ]);
    }

    return $collection;
  }

}
