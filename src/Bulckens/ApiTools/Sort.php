<?php

namespace Bulckens\ApiTools;

class Sort {

  protected $key;
  protected $way;

  public function __construct( $order ) {
    $parts = explode( self::delimiter(), $order );
    $this->key = $parts[0];
    $this->way = isset( $parts[1] ) ? $parts[1] : null;
  }


  // Get the sort order key
  public function key( $key = null ) {
    return $this->key ?: $key;
  }


  // Get the sort order direction
  public function way( $way = 'asc' ) {
    return $this->way ?: Api::get()->config( 'sort.way', $way );
  }


  // Generate sort order key
  public function get() {
    return self::order( $this->key(), $this->way() );
  }


  // Test if order is ascending
  public function asc( $fallback = 'asc' ) {
    return $this->way( $fallback ) == 'asc';
  }


  // Test if order is descending
  public function desc( $fallback = 'desc' ) {
    return $this->way( $fallback ) == 'desc';
  }


  // Get delimiter
  public static function delimiter() {
    return Api::get()->config( 'sort.delimiter', '-' );
  }


  // Generate sort order key
  public static function order( $key, $way ) {
    return $key . self::delimiter() . $way;
  }

}
