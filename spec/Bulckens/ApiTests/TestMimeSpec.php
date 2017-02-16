<?php

namespace spec\Bulckens\ApiTests;

use Bulckens\ApiTests\TestMime;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TestMimeSpec extends ObjectBehavior {

  // Type method
  function it_returns_the_corresponding_mime_type_for_given_format() {
    $this::type( 'json' )->shouldBe( 'application/json' );
  }

  function it_returns_the_full_mime_map_without_given_format() {
    $this::type()->shouldBeArray();
  }

  function it_fails_when_mime_type_does_not_exist() {
    $this::shouldThrow( 'Bulckens\ApiTools\MimeTypeMissingException' )->duringType( 'fra' );
  }


  // Comment method
  function it_creates_a_comment_from_a_string_for_js_format() {
    $this::comment( 'a string', 'js' )->shouldBe( "/*\na string\n*/" );
  }

  function it_creates_a_comment_from_a_string_for_css_format() {
    $this::comment( 'a string', 'css' )->shouldBe( "/*\na string\n*/" );
  }

  function it_creates_a_comment_from_a_string_for_html_format() {
    $this::comment( 'a string', 'html' )->shouldBe( "<!--\na string\n-->" );
  }

  function it_returns_the_given_value_if_no_special_formatting_is_required() {
    $this::comment( 'a string', 'lido' )->shouldBe( 'a string' );
  }

}
