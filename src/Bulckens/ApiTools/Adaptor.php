<?php

namespace Bulckens\ApiTools;

use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Bulckens\AppTools\App;
use Bulckens\AppTools\Statistics;

abstract class Adaptor {

  protected $output;
  protected $respond_to = 'all';
  protected $__req;
  protected $__res;
  protected $__args;

  // Build an action
  public function action( $action ) {
    $adaptor = $this;
    
    return function( $req, $res, $args ) use( $action, $adaptor ) {
      // store request and response
      $adaptor->req( $req )->res( $res )->args( $args );

      // get format with fallback
      $format = $adaptor->args( 'format', App::get()->router()->config( 'format' ) );

      // prepare output
      $adaptor->output = new Output( $format );

      if ( $format ) {
        // add current route
        $adaptor->output->path( $req->getUri()->getPath() );
        
        // call before
        if ( method_exists( $adaptor, 'before' ) )
          $adaptor->before();

        // call action
        $res = $adaptor->$action();
        
        // call after
        if ( method_exists( $adaptor, 'after' ) )
          $adaptor->after();

        return $res;
      }

      // add missing format error
      $adaptor->output->add([ 'error' => 'format.missing' ])->status( 406 );

      return $adaptor->render();
    };
  }


  // Render output
  public function render( $subject = null, $locals = [] ) {
    if ( is_string( $subject ) ) {
      // render view
      $output = App::get()->view()->render( $subject, $locals );

    } else {
      // get allowed formats
      $subject = $subject ?: $this->respond_to;

      // make sure to respond to only allowed formats
      if ( is_array( $subject ) && ! in_array( $this->args( 'format' ), $subject ) )
        $this->output->clear()
                     ->add([
                       'error'   => 'format.not_accepted'
                     , 'details' => [ 'accepted' => $subject ] ])
                     ->status( 406 );

      // add statistics
      $this->output->add([ 'statistics' => Statistics::toArray() ]);

      // render output
      $output = $this->output->render();
    }

    // add headers
    foreach ( $this->output->headers() as $header )
      $this->__res = $this->__res->withHeader( $header[0], $header[1] );

    // render output
    return $this->res()->withHeader( 'Content-type', $this->output->mime() )
                       ->withStatus( $this->output->status() )
                       ->write( $output );
  }


  // Get output object
  public function output() {
    return $this->output;
  }
  

  // Set/get request
  public function req( $req = null ) {
    if ( is_null( $req ) )
      return $this->__req;

    if ( $req instanceof Request )
      $this->__req = $req;
    else
      throw new AdaptorRequestInvalidException( 'Expected instance of Slim\Http\Request but got ' . get_class( $req ) );

    return $this;
  }


  // Set/get response
  public function res( $res = null ) {
    if ( is_null( $res ) )
      return $this->__res;

    if ( $res instanceof Response )
      $this->__res = $res;
    else
      throw new AdaptorResponseInvalidException( 'Expected instance of Slim\Http\Response but got ' . get_class( $res ) );

    return $this;
  }


  // Set/get arguments
  public function args( $args = null, $fallback = null ) {
    if ( is_null( $args ) )
      return $this->__args;

    else if ( is_string( $args ) )
      return isset( $this->__args[$args] ) ? $this->__args[$args] : $fallback;

    if ( is_array( $args ) )
      $this->__args = $args;
    else
      throw new AdaptorArgumentsInvalidException( 'Expected an array of arguments but got ' . get_class( $args ) );

    return $this;
  }
  
}


// Exceptions
class AdaptorRequestInvalidException extends Exception {}
class AdaptorResponseInvalidException extends Exception {}
class AdaptorArgumentsInvalidException extends Exception {}