<?php

namespace spec\Bulckens\ApiTests;

use Bulckens\AppTools\App;
use Bulckens\ApiTools\Api;
use Bulckens\ApiTools\Sort;
use Bulckens\ApiTests\TestModel;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestModelSpec extends ObjectBehavior {

  function let() {
    $app = new App( 'dev', __DIR__, 3 );
    $app->module( 'api', new Api() );
  }

  // Resource method
  function it_registers_a_given_uri_part() {
    $this->source( 'fake' )->resource( 'part' );
    $this->path()->shouldBe( '/part' );
  }
  
  function it_returns_itself_after_registering() {
    $this->source( 'fake' )->resource( 'part' )->shouldBe( $this );
  }

  function it_registers_a_given_uri_part_multiple_times() {
    $this->source( 'fake' )->resource( 'first' );
    $this->resource( 'second' );
    $this->resource( 'third' );
    $this->path()->shouldBe( '/first/second/third' );
  }

  function it_returns_a_resource_instance_with_registered_part() {
    $this->resource( 'api', 'v1' )->path()->shouldBe( '/api/v1' );
  }

  function it_returns_a_resource_instance_with_multiple_registered_parts() {
    $this->resource( 'api', 'v1' )->resource( 'flyers' )->path()->shouldBe( '/api/v1/flyers' );
  }

  function it_returns_itself_after_setting_the_resource() {
    $this->resource( 'api', 'v1' )->shouldBe( $this );
  }


  // Query method
  function it_returns_query_params_as_array() {
    $this->query( 'cloud', 'pink' );
    $this->query()->shouldBeArray();
    $this->query()->shouldHaveKey( 'cloud' );
  }

  function it_adds_a_key_value_pair_to_the_get_query_params() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'steam' )->query( 'cloud', 'pink' );
    $this->uri( 'json' )->shouldStartWith( '/steam.json?cloud=pink&token=' );
  }

  function it_adds_all_key_value_pairs_in_the_given_array_to_the_get_query_params() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'pole' )->query([
      'drip'     => 'drop'
    , 'drippidy' => 'drop drop'
    ]);
    $this->uri( 'xml' )->shouldStartWith( '/pole.xml?drip=drop&drippidy=drop+drop&token=' );
  }

  function it_returns_itself_after_adding_query_params() {
    $this->source( 'fake' )->resource( 'steam' )->query( 'cloud', 'pink' )->shouldBe( $this );
  }


  // Order method
  function it_adds_a_sort_order_key_value_pair_to_the_query() {
    $this->order( 'fee', 'desc' );
    $this->query()->shouldHaveKeyWithValue( 'order', 'fee-desc' );
  }

  function it_adds_the_values_form_a_sort_instance_to_the_query() {
    $this->order( new Sort( 'idx-asc' ) );
    $this->query()->shouldHaveKeyWithValue( 'order', 'idx-asc' );
  }


  // Data method
  function it_returns_data_as_array() {
    $this->data( 'light', 'yellow' );
    $this->data()->shouldBeArray();
    $this->data()->shouldHaveKey( 'light' );
  }

  function it_adds_a_key_value_pair_to_the_post_data_params() {
    $this->data( 'light', 'yellow' );
    $this->data()->shouldHaveKey( 'light' );
  }

  function it_adds_all_key_value_pairs_in_the_given_array_to_the_post_data_params() {
    $this->data([ 'rain' => 'falls', 'fog'  => 'flows' ]);
    $this->data()->shouldHaveKey( 'rain' );
    $this->data()->shouldHaveKey( 'fog' );
  }

  function it_returns_itself_after_adding_post_data_params() {
    $this->source( 'fake' )->resource( 'wind' )->data( 'light', 'yellow' )->shouldBe( $this );
  }


  // Source method
  function it_returns_the_source_server() {
    $this->source( 'fake' )->resource( 'path' );
    $this->source()->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_fails_without_a_source_server_key() {
    $this->resource( 'path' );
    $this->shouldThrow( 'Bulckens\ApiTools\ModelMissingSourceException' )->duringSource();
  }

  function it_sets_the_source_server() {
    $this->source( 'fake' )->source()->shouldBe( 'http://fake.zwartopwit.be' );
  }

  function it_sets_the_source_server_after_registering_a_resource() {
    $this->secret( 'generic' )->resource( 'path' )->source( 'fake' );
    $this->source()->shouldBe( 'http://fake.zwartopwit.be' );
    $this->url()->shouldStartWith( 'http://fake.zwartopwit.be/path.json?token=' );
  }

  function it_returns_itself_after_setting_the_source_server() {
    $this->source( 'fake' )->shouldBe( $this );
  }


  // Path method
  function it_returns_the_path_with_slash_prefix_and_format() {
    $this->resource( 'floo', 1 )->resource( 'fla' );
    $this->path( 'xml' )->shouldBe( '/floo/1/fla.xml' );
  }


  // Secret method
  function it_sets_a_given_secret_and_returns_the_stored_secret() {
    $this->secret( 'generic' )->secret()->shouldStartWith( '12345678910111213' );
  }

  function it_sets_the_given_secret_and_retuns_itself() {
    $this->secret( 'generic' )->shouldBe( $this );
  }

  function it_fails_without_a_secret_api_key() {
    $this->resource( 'path' );
    $this->shouldThrow( 'Bulckens\ApiTools\ModelMissingSecretException' )->duringSecret();
  }


  // Uri method
  function it_returns_the_full_uri() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'flyers', 1 )->resource( 'edit' );
    $this->uri()->shouldStartWith( '/flyers/1/edit.json?token=' );
  }

  function it_returns_the_full_uri_with_given_format() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'flyers', 1 )->resource( 'edit' );
    $this->uri( 'yaml' )->shouldStartWith( '/flyers/1/edit.yaml?token=' );
  }

  function it_returns_the_full_uri_with_auth_token() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'flyers' );
    $this->uri()->shouldMatch( '/^\/flyers\.json\?token=[a-z0-9]{75}$/' );
  }


  // Url method
  function it_returns_the_full_url() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'path' );
    $this->url()->shouldStartWith( 'http://fake.zwartopwit.be/path.json?token=' );
  }

  function it_returns_the_full_url_with_given_format() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'path' );
    $this->url( 'xml' )->shouldStartWith( 'http://fake.zwartopwit.be/path.xml?token=' );
  }

  function it_returns_the_full_url_with_ssl_enabled() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'path' );
    $this->url( 'xml', true )->shouldStartWith( 'https://fake.zwartopwit.be' );
  }

  function it_returns_the_full_url_with_ssl_disabled() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'path' );
    $this->url( 'xml', false )->shouldStartWith( 'http://fake.zwartopwit.be' );
  }


  // Get method
  function it_performs_a_get_request() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'flyers' );
    $this->get()->body()->shouldStartWith( '{"body":{"message":"success"' );
  }

  function it_returns_a_response_instance_on_performing_a_get_request() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'flyers' );
    $this->get()->shouldHaveType( 'Bulckens\ApiTools\Response' );
  }


  // Post method
  function it_performs_a_post_request() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'flyers' );
    $this->post()->body()->shouldStartWith( '{"body":{"message":"success"' );
  }

  function it_returns_a_response_instance_on_performing_a_post_request() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'flyers' );
    $this->post()->shouldHaveType( 'Bulckens\ApiTools\Response' );
  }


  // Put method
  function it_performs_a_put_request() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'flyers', 1 );
    $this->put()->body()->shouldStartWith( '{"body":{"message":"success"' );
  }

  function it_returns_a_response_instance_on_performing_a_put_request() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'flyers' );
    $this->put()->shouldHaveType( 'Bulckens\ApiTools\Response' );
  }


  // Delete method
  function it_performs_a_delete_request() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'flyers', 1 );
    $this->delete()->body()->shouldStartWith( '{"body":{"message":"success"' );
  }

  function it_returns_a_response_instance_on_performing_a_delete_request() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'flyers' );
    $this->delete()->shouldHaveType( 'Bulckens\ApiTools\Response' );
  }


  // Error codes
  function it_returns_a_401_error_with_an_invalid_api_secret() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'forbidden' );
    $this->get()->status()->shouldBe( 401 );
    $this->get()->parse()->shouldHaveKey( 'error' );
  }

  function it_returns_a_404_error_for_a_non_existing_resource() {
    $this->secret( 'generic' )->source( 'fake' )->resource( 'api-tools' )->resource( 'missing' );
    $this->get()->status()->shouldBe( 404 );
  }

}
