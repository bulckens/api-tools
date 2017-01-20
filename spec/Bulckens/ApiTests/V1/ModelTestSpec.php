<?php

namespace spec\Bulckens\ApiTests\V1;

use Bulckens\ApiTests\V1\ModelTest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ModelTestSpec extends ObjectBehavior {

  // Resource method
  function it_registers_a_given_uri_part() {
    $this->source( 'test' )->resource( 'part' );
    $this->part( 'path' )->shouldBe( 'part' );
  }
  
  function it_returns_itself_after_registering() {
    $this->source( 'test' )->resource( 'part' )->shouldBe( $this );
  }

  function it_registers_a_given_uri_part_multiple_times() {
    $this->source( 'test' )->resource( 'first' );
    $this->resource( 'second' );
    $this->resource( 'third' );
    $this->part( 'path' )->shouldBe( 'first/second/third' );
  }

  function it_returns_a_resource_instance_with_registered_part() {
    $this->resource( 'api', 'v1' )->part( 'path' )->shouldBe( 'api/v1' );
  }

  function it_returns_a_resource_instance_with_multiple_registered_parts() {
    $this->resource( 'api', 'v1' )->resource( 'flyers' )->part( 'path' )->shouldBe( 'api/v1/flyers' );
  }

  function it_returns_itself_after_setting_the_resource() {
    $this->resource( 'api', 'v1' )->shouldBe( $this );
  }


  // Query method
  function it_adds_a_key_value_pair_to_the_get_query_params() {
    $this->source( 'test' )->resource( 'steam' )->query( 'cloud', 'pink' );
    $this->uri( 'json' )->shouldStartWith( '/steam.json?cloud=pink&token=' );
  }

  function it_adds_all_key_value_pairs_in_the_given_array_to_the_get_query_params() {
    $this->source( 'test' )->resource( 'pole' )->query([
      'drip'     => 'drop'
    , 'drippidy' => 'drop drop'
    ]);
    $this->uri( 'xml' )->shouldStartWith( '/pole.xml?drip=drop&drippidy=drop+drop&token=' );
  }

  function it_returns_itself_after_adding_query_params() {
    $this->source( 'test' )->resource( 'steam' )->query( 'cloud', 'pink' )->shouldBe( $this );
  }


  // Source method
  function it_returns_the_source_server() {
    $this->source( 'test' )->resource( 'path' );
    $this->source()->shouldBe( 'http://fake.zwartopwit.be/api-tools' );
  }

  function it_fails_without_a_source_server_key() {
    $this->resource( 'path' );
    $this->shouldThrow( 'Bulckens\ApiTools\V1\MissingServerException' )->duringSource();
  }

  function it_sets_the_source_server() {
    $this->source( 'test' )->source()->shouldBe( 'http://fake.zwartopwit.be/api-tools' );
  }

  function it_sets_the_source_server_after_registering_a_resource() {
    $this->resource( 'path' )->source( 'test' );
    $this->source()->shouldBe( 'http://fake.zwartopwit.be/api-tools' );
    $this->url()->shouldStartWith( 'http://fake.zwartopwit.be/api-tools/path.json?token=' );
  }

  function it_returns_itself_after_setting_the_source_server() {
    $this->source( 'test' )->shouldBe( $this );
  }


  // Path method
  function it_returns_the_path_with_slash_prefix_and_format() {
    $this->resource( 'floo', 1 )->resource( 'fla' );
    $this->path( 'xml' )->shouldBe( '/floo/1/fla.xml' );
  }


  // Uri method
  function it_returns_the_full_uri() {
    $this->source( 'test' )->resource( 'flyers', 1 )->resource( 'edit' );
    $this->uri()->shouldStartWith( '/flyers/1/edit.json?token=' );
  }

  function it_returns_the_full_uri_with_given_format() {
    $this->source( 'test' )->resource( 'flyers', 1 )->resource( 'edit' );
    $this->uri( 'yaml' )->shouldStartWith( '/flyers/1/edit.yaml?token=' );
  }

  function it_returns_the_full_uri_with_auth_token() {
    $this->source( 'test' )->resource( 'flyers' );
    $this->uri()->shouldMatch( '/^\/flyers\.json\?token=[a-z0-9]{75}$/' );
  }


  // Url method
  function it_returns_the_full_url() {
    $this->source( 'test' )->resource( 'path' );
    $this->url()->shouldStartWith( 'http://fake.zwartopwit.be/api-tools/path.json?token=' );
  }

  function it_returns_the_full_url_with_given_format() {
    $this->source( 'test' )->resource( 'path' );
    $this->url( 'xml' )->shouldStartWith( 'http://fake.zwartopwit.be/api-tools/path.xml?token=' );
  }

  function it_returns_the_full_url_with_ssl_enabled() {
    $this->source( 'test' )->resource( 'path' );
    $this->url( 'xml', true )->shouldStartWith( 'https://fake.zwartopwit.be/api-tools' );
  }

  function it_returns_the_full_url_with_ssl_disabled() {
    $this->source( 'test' )->resource( 'path' );
    $this->url( 'xml', false )->shouldStartWith( 'http://fake.zwartopwit.be/api-tools' );
  }


  // Part method
  function it_returns_the_source_part() {
    $this->source( 'test' )->resource( 'path' );
    $this->part( 'source' )->shouldBe( 'test' );
  }

  function it_returns_the_source_part_even_without_a_given_source() {
    $this->resource( 'path' );
    $this->part( 'source' )->shouldBe( '' );
  }


  // Parts method
  function it_returns_uri_parts_as_an_array() {
    $this->source( 'test' )->resource( 'path' );
    $this->parts()->shouldBeArray();
    $this->parts()->shouldBe([ 'test', 'path' ]);
  }

  function it_returns_the_uri_part_at_the_given_index() {
    $this->source( 'test' )->resource( 'path' );
    $this->parts( 0 )->shouldBe( 'test' );
    $this->parts( 1 )->shouldBe( 'path' );
  }


  // Get method
  function it_performs_a_get_request() {
    $this->source( 'test' )->resource( 'flyers' );
    $this->get()->body()->shouldStartWith( '{"body":{"message":"success"' );
  }

  function it_returns_a_response_instance_on_performing_a_get_request() {
    $this->source( 'test' )->resource( 'flyers' );
    $this->get()->shouldHaveType( 'Bulckens\ApiTools\V1\Response' );
  }


  // Post method
  function it_performs_a_post_request() {
    $this->source( 'test' )->resource( 'flyers' );
    $this->post()->body()->shouldStartWith( '{"body":{"message":"success"' );
  }

  function it_returns_a_response_instance_on_performing_a_post_request() {
    $this->source( 'test' )->resource( 'flyers' );
    $this->post()->shouldHaveType( 'Bulckens\ApiTools\V1\Response' );
  }


  // Put method
  function it_performs_a_put_request() {
    $this->source( 'test' )->resource( 'flyers', 1 );
    $this->put()->body()->shouldStartWith( '{"body":{"message":"success"' );
  }

  function it_returns_a_response_instance_on_performing_a_put_request() {
    $this->source( 'test' )->resource( 'flyers' );
    $this->put()->shouldHaveType( 'Bulckens\ApiTools\V1\Response' );
  }


  // Delete method
  function it_performs_a_delete_request() {
    $this->source( 'test' )->resource( 'flyers', 1 );
    $this->delete()->body()->shouldStartWith( '{"body":{"message":"success"' );
  }

  function it_returns_a_response_instance_on_performing_a_delete_request() {
    $this->source( 'test' )->resource( 'flyers' );
    $this->delete()->shouldHaveType( 'Bulckens\ApiTools\V1\Response' );
  }

}
