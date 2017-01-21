<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\ApiTools\V1\Response;
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


  // Status
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

}