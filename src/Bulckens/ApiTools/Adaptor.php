<?php

namespace Bulckens\ApiTools;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Bulckens\Helpers\UploadHelper;
use Bulckens\AppTools\App;
use Bulckens\AppTools\Output;

abstract class Adaptor {

  protected $output;
  protected $params;
  protected $wrapper;
  protected $respond_to = 'all';
  protected $__req;
  protected $__res;
  protected $__args;

  // Build an action
  final public function action( $name ) {
    if ( method_exists( $this, $name ) ) {
      return new Action( $name, $this );
    }
  }


  // Render output
  public function render( $subject = null, $locals = [] ) {
    if ( is_string( $subject ) ) {
      // merge info in locals
      $locals = array_replace( $this->information(), $this->output()->toArray(), $locals );

      // render view
      $output = App::get()->view()->render( $subject, $locals );

    } else {
      // get allowed formats
      $subject = $subject ?: $this->respond_to;

      // make sure to respond to only allowed formats
      if ( is_array( $subject ) && ! in_array( $this->args( 'format' ), $subject ) ) {
        $this
          ->output
          ->clear()
          ->add([
            'error'   => 'format.not_accepted'
          , 'details' => [ 'accepted' => $subject ]
          ])
          ->status( 406 );
      }

      // add additional info
      if ( Api::get()->config( 'verbose' ) ) {
        $this->output->add( $this->information() );
      }

      // render output
      $output = $this->output->render();
    }

    // add headers
    foreach ( $this->output->headers() as $header ) {
      $this->__res = $this->__res->withHeader( $header[0], $header[1] );
    }

    // return wrapped output
    if ( is_string( $wrapper = $this->wrapper ) ) {
      return new $wrapper( $output );
    }

    // render output
    return $this
      ->res()->withHeader( 'Content-type', $this->output->mime() )
      ->withStatus( $this->output->status() )
      ->write( $output );
  }


  // Get output object
  final public function output( $format = null ) {
    if ( is_null( $format ) ) return $this->output;

    $this->output = new Output( $format );

    return $this;
  }
  

  // Set/get request
  final public function req( $req = null ) {
    if ( is_null( $req ) ) return $this->__req;

    if ( $req instanceof Request ) {
      $this->__req = $req;
    } else {
      throw new AdaptorRequestInvalidException( 'Expected instance of Slim\Http\Request but got ' . get_class( $req ) );
    }

    return $this;
  }


  // Set/get response
  final public function res( $res = null ) {
    if ( is_null( $res ) ) return $this->__res;

    if ( $res instanceof Response ) {
      $this->__res = $res;
    } else {
      throw new AdaptorResponseInvalidException( 'Expected instance of Slim\Http\Response but got ' . get_class( $res ) );
    }

    return $this;
  }


  // Set/get arguments
  final public function args( $args = null, $fallback = null ) {
    if ( is_null( $args ) ) {
      return $this->__args;

    } else if ( is_string( $args ) ) {
      return isset( $this->__args[$args] ) ? $this->__args[$args] : $fallback;
    }

    if ( is_array( $args ) ) {
      $this->__args = $args;
    } else {
      throw new AdaptorArgumentsInvalidException( 'Expected an array of arguments but got ' . get_class( $args ) );
    }

    return $this;
  }


  // Get parameters combined with file post data
  public function params( $key = null ) {
    // cache params
    if ( ! isset( $this->params ) ) {
      $this->params = array_replace_recursive( $this->req()->getParams(), UploadHelper::files() );
    }

    // return the whole array of params if no key is given
    if ( is_null( $key ) ) return $this->params;

    // return the param for the given key if it exists
    if ( isset( $this->params[$key] ) ) return $this->params[$key];
  }


  // Set output wrapper
  final public function wrapper( $wrapper ) {
    $this->wrapper = $wrapper;
    return $this;
  }


  // Get request information
  final public function information() {
    // get current uri info
     $uri = $this->req()->getUri();

     // addition app and request info
     return [
       'statistics' => App::get()->statistics()->toArray()
     , 'request' => [
         'base' => $uri->getBaseUrl()
       , 'scheme' => $uri->getScheme()
       , 'host' => $uri->getHost()
       , 'port' => $uri->getPort()
       , 'path' => $uri->getPath()
       , 'params' => $this->req()->getParams()
       ] 
     ];
  }
  
}


// Exceptions
class AdaptorRequestInvalidException extends Exception {}
class AdaptorResponseInvalidException extends Exception {}
class AdaptorArgumentsInvalidException extends Exception {}
