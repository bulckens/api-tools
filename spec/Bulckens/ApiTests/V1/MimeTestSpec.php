<?php

namespace spec\Bulckens\ApiTests\V1;

use Bulckens\ApiTests\V1\MimeTest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MimeTestSpec extends ObjectBehavior {

  function it_returns_the_corresponding_mime_type_for_given_format() {
    $this::type( 'json' )->shouldBe( 'application/json' );
  }

  function it_returns_the_full_mime_map_without_given_format() {
    $this::type()->shouldBeArray();
  }

  function it_creates_a_comment_from_a_string_for_js_format() {
    $this::comment( 'a string', 'js' )->shouldBe( "/*\na string\n*/" );
  }

  function it_creates_a_comment_from_a_string_for_css_format() {
    $this::comment( 'a string', 'css' )->shouldBe( "/*\na string\n*/" );
  }

  function it_creates_a_comment_from_a_string_for_html_format() {
    $this::comment( 'a string', 'html' )->shouldBe( "<!--\na string\n-->" );
  }

}
