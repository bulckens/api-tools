<?php

namespace Bulckens\ApiTools\V1;

use Bulckens\Statistics\Statistics;

abstract class Adaptor {

  protected $output;
  protected $respond_to = 'all';
  public $__req;
  public $__res;
  public $__args;

  // Build an action
  public function action( $action ) {
    $adaptor = $this;
    
    return function( $req, $res, $args ) use( $action, $adaptor ) {
      // store request and response
      $adaptor->req( $req )->res( $res )->args( $args );

      if ( $adaptor->args( 'format' ) ) {
        // prepare output
        $adaptor->output = new Output( $adaptor->args( 'format' ) );

        // add current route
        $adaptor->output->path( $req->getUri()->getPath() );
        
        // call before
        if ( method_exists( $adaptor, 'before' ) )
          $adaptor->before( $req, $res, $args );

        // call action
        $res = $adaptor->$action( $req, $res, $args );
        
        // call after
        if ( method_exists( $adaptor, 'after' ) )
          $adaptor->after( $req, $res, $args );

        return $res;

      } else {
        $adaptor->output->add([ 'error' => 'format.missing' ])->status( 406 );
      }

      return $adaptor->render();
    };
  }

  // Render output
  protected function render( $formats = null ) {
    // get allowed formats
    $formats = $formats ?: $this->respond_to;

    // make sure to respond to only allowed formats
    if ( is_array( $formats ) && ! in_array( $this->args( 'format' ), $formats ) )
      $this->output->clear()
                   ->add([
                     'error'   => 'format.not_accepted'
                   , 'details' => [ 'accepted' => $formats ] ])
                   ->status( 406 );

    // add statistics
    $this->output->add([ 'statistics' => Statistics::toArray() ]);

    // add headers
    foreach ( $this->output->headers() as $header )
      $this->__res = $this->__res->withHeader( $header[0], $header[1] );

    // render output
    return $this->res()->withHeader( 'Content-type', $this->output->mime() )
                       ->withStatus( $this->output->status() )
                       ->write( $this->output->render() );
  }

  // Set/get request
  protected function req( $req = null ) {
    if ( is_null( $req ) )
      return $this->__req;

    $this->__req = $req;

    return $this;
  }

  // Set/get response
  protected function res( $res = null ) {
    if ( is_null( $res ) )
      return $this->__res;

    $this->__res = $res;

    return $this;
  }

  // Set/get arguments
  protected function args( $args = null ) {
    if ( is_null( $args ) )
      return $this->__args;

    else if ( is_string( $args ) && isset( $this->__args[$args] ) )
      return $this->__args[$args];

    else if ( is_array( $args ) )
      $this->__args = $args;

    return $this;
  }
  
}
