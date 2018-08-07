<?php

namespace Bulckens\ApiTools;

use Exception;
use Bulckens\AppTools\App;

class Action {

  protected $name;
  protected $adaptor;

  public function __construct( $name, $adaptor ) {
    $this->name    = $name;
    $this->adaptor = $adaptor;
  }


  // Callable
  public function __invoke( $req, $res, $args ) {
    // test if action is callable
    if ( ! method_exists( $this->adaptor, $action = $this->name ) )
      throw new ActionMissingException( 'Undefined method ' . get_class( $this->adaptor ) . "::$action" ); 

    // store request and response
    $this->adaptor->req( $req )->res( $res )->args( $args );

    // get format with fallback
    $format = $this->adaptor->args( 'format', App::get()->router()->config( 'format' ) );
    
    // prepare output
    $this->adaptor->output( $format );

    if ( $format ) {
      // add current route
      $this->adaptor->output()->path( $req->getUri()->getPath() );
      
      // call before
      if ( method_exists( $this->adaptor, 'before' ) )
        $this->adaptor->before( $this->name );

      // call action
      $res = $this->adaptor->$action();
      
      // call after
      if ( method_exists( $this->adaptor, 'after' ) )
        $this->adaptor->after( $this->name );

      return $res;
    }

    // add missing format error
    $this->adaptor->output()->add([ 'error' => 'format.missing' ])->status( 406 );

    return $this->adaptor->render();
  }

}


// Exceptions
class ActionMissingException extends Exception {}

