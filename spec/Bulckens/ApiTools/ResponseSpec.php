<?php

namespace spec\Bulckens\ApiTools ;

use Bulckens\ApiTools\Response;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseSpec extends ObjectBehavior {

  function let() {
    $this->beConstructedWith( 'json' );
  }

  
  // Format method
  function it_returns_the_format() {
    $this->format()->shouldBe( 'json' );
  }

  function it_tests_positive_with_the_given_format() {
    $this->format( 'json' )->shouldBe( true );
  }

  function it_tests_negative_with_the_wrong_given_format() {
    $this->format( 'xml' )->shouldBe( false ); 
  }

  function it_tests_positive_with_the_format_in_a_given_array() {
    $this->format([ 'xml', 'json' ])->shouldBe( true );
  }

  function it_tests_negative_with_the_format_not_in_a_given_array() {
    $this->format([ 'xml', 'yaml' ])->shouldBe( false );
  }


  // Status method
  function it_returns_the_status() {
    $this->status( 404 );
    $this->status()->shouldBe( 404 );
  }

  function it_sets_the_status() {
    $this->status( 200 )->status()->shouldBe( 200 );
  }

  function it_returns_itself_after_setting_the_status() {
    $this->status( 418 )->shouldBe( $this );
  }


  // Headers method
  function it_returns_the_headers() {
    $this->headers([ 'Expires' => 'Wed, 03 May 2017 14:16:34 GMT' ]);
    $headers = $this->headers();
    $headers->shouldBeArray();
    $headers->shouldHaveKeyWithValue( 'Expires', 'Wed, 03 May 2017 14:16:34 GMT' );
  }

  function it_returns_a_single_header() {
    $this->headers([ 'Expires' => 'Wed, 03 May 2017 14:16:34 GMT' ]);
    $this->headers( 'Expires' )->shouldBe( 'Wed, 03 May 2017 14:16:34 GMT' );
  }

  function it_sets_the_headers() {
    $this->headers([ 'Expires' => 'Wed, 03 May 2017 14:16:34 GMT' ]);
    $this->headers()->shouldHaveCount( 1 );
  }

  function it_returns_itself_after_setting_the_headers() {
    $this->headers([ 'Expires' => 'Wed, 03 May 2017 14:16:34 GMT' ])->shouldBe( $this );
  }


  // OK method
  function it_is_ok_with_a_status_code_under_400() {
    $this->status( 200 )->ok()->shouldBe( true );
    $this->status( 302 )->ok()->shouldBe( true );
    $this->status( 308 )->ok()->shouldBe( true );
  }

  function it_is_not_ok_with_a_status_code_over_400() {
    $this->status( 400 )->ok()->shouldBe( false );
    $this->status( 404 )->ok()->shouldBe( false );
    $this->status( 500 )->ok()->shouldBe( false );
  }


  // Body method
  function it_returns_the_body() {
    $this->body( 'Somebody' );
    $this->body()->shouldBe( 'Somebody' );
  }

  function it_sets_the_body() {
    $this->body( 'Some other body' )->body()->shouldBe( 'Some other body' );
  }

  function it_returns_itself_after_setting_the_body() {
    $this->body( 'Some other body' )->shouldBe( $this );
  }


  // Parse method
  function it_parses_the_json_body_to_an_array() {
    $this->body( '{"feel":"good"}' );
    $this->parse()->shouldBeArray();
    $this->parse()->shouldBe([ 'feel' => 'good' ]);
  }

  function it_parses_the_xml_body_to_an_array() {
    $this->beConstructedWith( 'xml' );
    $this->body( '<root><feel>good</feel></root>' );
    $this->parse()->shouldBeArray();
    $this->parse()->shouldBe([ 'feel' => 'good' ]);
  }

  function it_parses_the_yaml_body_to_an_array() {
    $this->beConstructedWith( 'yaml' );
    $this->body( "feel: good\n" );
    $this->parse()->shouldBeArray();
    $this->parse()->shouldBe([ 'feel' => 'good' ]);
  }

  function it_does_not_parse_a_html_body() {
    $this->beConstructedWith( 'html' );
    $this->body( '<html><head><title></title></head><body></body></html>' );
    $this->parse()->shouldBeString();
    $this->parse()->shouldBe( '<html><head><title></title></head><body></body></html>' );
  }


  // Attr method
  function it_returns_a_specific_attribute_from_the_parsed_body() {
    $this->body( '{"be":"smart","feel":"good"}' );
    $this->attr( 'be' )->shouldBe( 'smart' );
    $this->attr( 'feel' )->shouldBe( 'good' );
  }

  function it_returns_a_nested_attribute_from_the_parsed_body() {
    $this->body( '{"one":{"in":{"another":"two"}}}' );
    $this->attr( 'one.in.another' )->shouldBe( 'two' );
  }

  function it_returns_an_array_of_mapped_attributes_from_a_collection() {
    $this->body( '{"collection":[{"id":3,"name":"three"},{"id":13,"name":"thirteen"}]}' );
    $collection = $this->attr( 'collection.@id' );
    $collection->shouldHaveCount( 2 );
    $collection->shouldContain( 3 );
    $collection->shouldContain( 13 );
  }

  function it_returns_a_single_item_from_a_collection() {
    $this->body( '{"collection":[{"id":3,"name":"three"},{"id":5,"name":"five"},{"id":13,"name":"thirteen"}]}' );
    $collection = $this->attr( 'collection.#1' );
    $collection->shouldHaveKeyWithValue( 'id', 5 );
    $collection->shouldHaveKeyWithValue( 'name', 'five' );
  }

  function it_returns_the_last_item_from_a_collection() {
    $this->body( '{"collection":[{"id":3,"name":"three"},{"id":13,"name":"thirteen"}]}' );
    $collection = $this->attr( 'collection.#n' );
    $collection->shouldHaveKeyWithValue( 'id' , 13 );
    $collection->shouldHaveKeyWithValue( 'name' , 'thirteen' );
  }

  function it_returns_null_if_a_nested_attribute_does_not_exist() {
    $this->body( '{"one":{"in":{"another":"two"}}}' );
    $this->attr( 'no.one.in.there' )->shouldBe( null );
  }

  function it_returns_a_null_if_the_attribute_does_not_exist() {
    $this->body( '{"feel":"good"}' );
    $this->attr( 'be' )->shouldBe( null );
  }

  function it_returns_a_nothing_if_the_parsed_body_is_not_an_array() {
    $this->beConstructedWith( 'html' );
    $this->body( '<html><head><title></title></head><body></body></html>' );
    $this->attr( 'title' )->shouldBe( null );
  }

}