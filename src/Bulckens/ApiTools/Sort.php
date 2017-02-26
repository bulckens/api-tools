<?php

namespace Bulckens\ApiTools;

class Sort {

  protected $key;
  protected $way;

  public function __construct( $order ) {
    $parts = explode( '-', $order );
    $this->key = $parts[0];
    $this->way = isset( $parts[1] ) ? $parts[1] : null;
  }


  // Get the sort order key
  public function key( $key = null ) {
    return $this->key ?: $key;
  }


  // Get the sort order direction
  public function way( $way = 'asc' ) {
    return $this->way ?: $way;
  }

}
