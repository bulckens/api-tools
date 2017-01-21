<?php

namespace spec\Bulckens\ApiTools\V1;

use Bulckens\ApiTools\V1\Output;
use Bulckens\ApiTools\V1\Config;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OutputSpec extends ObjectBehavior {

  function let() {
    $this->beConstructedWith( 'json' );
  }

  // Initializing
  function it_initializes_with_given_format() {
    $this->format()->shouldBe( 'json' );
  }

  function it_initializes_with_status_200() {
    $this->status()->shouldBe( 200 );
  }

  function it_initializes_with_empty_output() {
    $this->beConstructedWith( 'array' );
    $this->toArray()->shouldBeArray();
    $this->toArray()->shouldBe([]);
  }

  function it_initializes_with_empty_headers() {
    $this->headers()->shouldBeArray();
    $this->headers()->shouldBe([]);
  }

  function it_initializes_ok() {
    $this->ok()->shouldBe( true );
  }


  // Add method
  function it_accepts_an_array_for_add() {
    $this->add([ 'an' => 'array' ]);
  }

  function it_returns_itself_after_add() {
    $this->add([ 'an' => 'array' ])->shouldBe( $this );
  }

  function it_does_not_accept_anything_other_than_an_array_for_add() {
    $this->shouldThrow( 'Bulckens\ApiTools\V1\OutputArgumentInvalidException' )->duringAdd( 'string' );
    $this->shouldThrow( 'Bulckens\ApiTools\V1\OutputArgumentInvalidException' )->duringAdd( null );
    $this->shouldThrow( 'Bulckens\ApiTools\V1\OutputArgumentInvalidException' )->duringAdd( 123 );
    $this->shouldNotThrow( 'Bulckens\ApiTools\V1\OutputArgumentInvalidException' )->duringAdd([ 'I' => 'can' ]);
  }


  // Clear method
  function it_can_clear_output() {
    $this->beConstructedWith( 'array' );
    $this->add([ 'arbitrary' => 'data' ]);
    $this->toArray()->shouldHaveCount( 1 );
    $this->clear();
    $this->toArray()->shouldHaveCount( 0 );
  }

  function it_can_clear_headers() {
    $this->header( 'Hey', 'DIE!' );
    $this->headers()->shouldHaveCount( 1 );
    $this->clear();
    $this->headers()->shouldHaveCount( 0 );
  }

  function it_returns_itself_after_clear() {
    $this->clear()->shouldBe( $this );
  }


  // Header method
  function it_can_add_a_header() {
    $this->headers()->shouldHaveCount( 0 );
    $this->header( 'Pragma', 'public' );
    $this->headers()->shouldHaveCount( 1 );
  }

  function it_returns_itself_after_setting_header() {
    $this->header( 'Pragma', 'public' )->shouldBe( $this );
  }


  // Headers method
  function it_returns_an_array_of_headers() {
    $this->headers()->shouldBeArray();
  }

  function it_retreives_all_headers() {
    $this->headers()->shouldHaveCount( 0 );
    $this->header( 'Pragma', 'public' );
    $this->header( 'Cache-Control', 'maxage=3600' );
    $this->header( 'Expires', 'never' );
    $this->headers()->shouldHaveCount( 3 );
  }


  // Expires method
  function it_sets_required_expires_headers() {
    $this->headers()->shouldHaveCount( 0 );
    $this->expires( 3600 );
    $this->headers()->shouldHaveCount( 3 );
  }

  function it_returns_itself_after_setting_expiration() {
    $this->expires( 3600 )->shouldBe( $this );
  }

  function it_expires_without_an_argument() {
    $this->expires()->shouldBe( $this );
  }


  // Mime method
  function it_returns_mime_type_for_css_format() {
    $this->beConstructedWith( 'css' );
    $this->mime()->shouldBe( 'text/css' );
  }

  function it_returns_mime_type_for_dump_format() {
    $this->beConstructedWith( 'dump' );
    $this->mime()->shouldBe( 'text/plain' );
  }

  function it_returns_mime_type_for_html_format() {
    $this->beConstructedWith( 'html' );
    $this->mime()->shouldBe( 'text/html' );
  }

  function it_returns_mime_type_for_js_format() {
    $this->beConstructedWith( 'js' );
    $this->mime()->shouldBe( 'application/javascript' );
  }

  function it_returns_mime_type_for_json_format() {
    $this->beConstructedWith( 'json' );
    $this->mime()->shouldBe( 'application/json' );
  }

  function it_returns_mime_type_for_txt_format() {
    $this->beConstructedWith( 'txt' );
    $this->mime()->shouldBe( 'text/plain' );
  }

  function it_returns_mime_type_for_xml_format() {
    $this->beConstructedWith( 'xml' );
    $this->mime()->shouldBe( 'application/xml' );
  }

  function it_returns_mime_type_for_yaml_format() {
    $this->beConstructedWith( 'yaml' );
    $this->mime()->shouldBe( 'application/x-yaml' );
  }


  // Status method
  function it_returns_the_default_status() {
    $this->status()->shouldBe( 200 );
  }

  function it_returns_the_given_status() {
    $this->status( 404 );
    $this->status()->shouldBe( 404 );
  }

  function it_sets_the_given_status() {
    $this->status()->shouldBe( 200 );
    $this->status( 500 );
    $this->status()->shouldBe( 500 );
  }

  function it_returns_itself_after_setting_status() {
    $this->status( 418 )->shouldBe( $this );
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


  // toArray method
  function it_returns_the_output_array() {
    $this->toArray()->shouldBeArray();
    $this->add([ 'fab' => 'ulous' ]);
    $this->toArray()->shouldHaveKey( 'fab' );
  }


  // Is method
  function it_tests_positive_when_the_given_format_is_the_initialized_format() {
    $this->beConstructedWith( 'xml' );
    $this->is( 'xml' )->shouldBe( true );
  }

  function it_tests_negative_when_the_given_format_is_the_initialized_format() {
    $this->beConstructedWith( 'js' );
    $this->is( 'json' )->shouldBe( false );
  }


  // Path method
  function it_gets_the_path() {
    $this->path()->shouldBe( null );
  }

  function it_sets_the_given_path() {
    $this->path( '/floop' );
    $this->path()->shouldBe( '/floop' );
  }


  // Purify method
  function it_purifies_the_output_when_the_status_is_ok() {
    $this->beConstructedWith( 'array' );
    $this->add([
      'error'    => 'bad'
    , 'success'  => 'good'
    , 'details'  => [ 'unimportant' => 'right now' ]
    , 'resource' => 'which is not there'
    ]);
    $this->status( 200 )->purify();
    $this->render()->shouldHaveKey( 'success' );
    $this->render()->shouldHaveKey( 'resource' );
    $this->render()->shouldHaveKey( 'details' );
    $this->render()->shouldNotHaveKey( 'error' );
  }

  function it_purifies_the_output_when_the_status_is_not_ok() {
    $this->beConstructedWith( 'array' );
    $this->add([
      'error'    => 'bad'
    , 'success'  => 'good'
    , 'details'  => [ 'unimportant' => 'right now' ]
    , 'resource' => 'which is not there'
    ]);
    $this->status( 418 )->purify();
    $this->render()->shouldHaveKey( 'error' );
    $this->render()->shouldHaveKey( 'details' );
    $this->render()->shouldNotHaveKey( 'success' );
    $this->render()->shouldNotHaveKey( 'resource' );
  }


  // Render method
  function it_renders_the_output_as_json() {
    $this->beConstructedWith( 'json' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldBe( '{"candy":{"ken":"pink"}}' );
  }

  function it_renders_the_output_as_yaml() {
    $this->beConstructedWith( 'yaml' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldBe( "candy:\n  ken: pink\n" );
  }

  function it_renders_the_output_as_xml() {
    $this->beConstructedWith( 'xml' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldBe( "<?xml version=\"1.0\"?>\n<root><candy><ken>pink</ken></candy></root>\n" );
  }

  function it_renders_the_output_as_dump() {
    $this->beConstructedWith( 'dump' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldBe( "Array\n(\n    [candy] => Array\n        (\n            [ken] => pink\n        )\n\n)\n" );
  }

  function it_renders_the_output_as_array() {
    $this->beConstructedWith( 'array' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldBe( [ 'candy' => [ 'ken' => 'pink' ] ] );
  }

  function it_renders_the_output_as_html() {
    $this->beConstructedWith( 'html' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldStartWith( "<!--\nArray" );
  }

  function it_renders_the_output_as_txt() {
    $this->beConstructedWith( 'txt' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldStartWith( "Array\n" );
  }

  function it_renders_the_output_as_css() {
    $this->beConstructedWith( 'css' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldStartWith( "/*\nArray" );
  }

  function it_renders_the_output_as_js() {
    $this->beConstructedWith( 'js' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldStartWith( "/*\nArray" );
  }

  function it_does_not_accept_any_other_formats() {
    $this->beConstructedWith( 'png' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->shouldThrow( 'Bulckens\ApiTools\V1\OutputFormatUnknownException' )->duringRender();
  }

  function it_uses_the_alternative_render_method() {
    Config::file( 'api_tools.render.yml' );

    $this->beConstructedWith( 'html' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->render()->shouldBe( '<html><head><title></title></head><body>Rendered from the outside!</body></html>' );

    Config::file( 'api_tools.yml' );
  }

  function it_fails_if_the_defined_render_method_is_not_callable() {
    Config::file( 'api_tools.render_fail.yml' );

    $this->beConstructedWith( 'html' );
    $this->add([ 'candy' => [ 'ken' => 'pink' ] ]);
    $this->shouldThrow( 'Bulckens\ApiTools\V1\OutputRenderMethodNotCallableException' )->duringRender();

    Config::file( 'api_tools.yml' );
  }

  function it_only_outputs_an_error_when_the_status_is_not_ok() {
    $this->add([ 'fine' => 'young canibals' ])->status( 418 );
    $this->render()->shouldBe( '{"error":"errors.418"}' );
  }

}
