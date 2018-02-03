<?php

namespace Bulckens\ApiTools;

use Exception;

class Paginate {

  protected $value = [
    'page' => 1
  , 'per_page' => 15
  ];

  public function __construct( $items, $options = [] ) {
    // store items
    if ( is_a( $items, 'Illuminate\Database\Eloquent\Collection' )) {
      $this->value['total'] = $items->count();
    } elseif ( is_array( $items )) {
      $this->value['total'] = count( $items );
    } elseif ( is_int( $items )) {
      $this->value['total'] = $items;
    } else {
      throw new PaginateItemsNotAcceptableException( 'Expected Eloquent Collection, array or integer but got ' . gettype( $items ) );
    }

    // store options
    if ( is_numeric( $options )) {
      $this->value['per_page'] = $options;
    } else {
      if ( isset( $options['page'] )) {
        $this->value['page'] = $options['page'];
      }

      if ( isset( $options['per_page'] )) {
        $this->value['per_page'] = $options['per_page'];
      }
    }

    // store total pages
    if ( empty( $this->value['total'] ) ) {
      $this->value['pages'] = 0;
    } else {
      $this->value['pages'] = intval( ceil( $this->value['total'] / $this->value['per_page'] ) );

      // store range
      $start = ( $this->value['page'] - 1 ) * $this->value['per_page'] + 1;
      $end = $this->value['page'] * $this->value['per_page'];

      $this->value['range'] = [
        $start
      , $end
      , 'start' => $start
      , 'end' => $end
      ];
    }

    // store next
    if ( $this->value['page'] < $this->value['pages'] ) {
      $this->value['next'] = $this->value['page'] + 1;
    }

    // store previous
    if ( $this->value['page'] > 1 ) {
      $this->value['previous'] = $this->value['page'] - 1;
    }

    // store necessary
    $this->value['necessary'] = $this->value['pages'] > 1;
  }


  // Convert to array
  public function toArray() {
    return $this->value;
  }


  // Get property
  public function __get( $name ) {
    if ( isset( $this->value[$name] )) {
      return $this->value[$name];
    }
  }

}


// Exceptions
class PaginateItemsNotAcceptableException extends Exception {}

