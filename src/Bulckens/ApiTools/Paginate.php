<?php

namespace Bulckens\ApiTools;

use Exception;

class Paginate {

  public $total;
  public $pages;
  public $range;
  public $next;
  public $previous;
  public $necessary;
  public $page = 1;
  public $per_page = 15;

  public function __construct( $items, $options = [] ) {
    // store items
    if ( is_a( $items, 'Illuminate\Database\Eloquent\Collection' )) {
      $this->total = $items->count();
    } elseif ( is_array( $items )) {
      $this->total = count( $items );
    } elseif ( is_int( $items )) {
      $this->total = $items;
    } else {
      throw new PaginateItemsNotAcceptableException( 'Expected Eloquent Collection, array or integer but got ' . gettype( $items ));
    }

    // store options
    if ( is_numeric( $options )) {
      $this->per_page = $options;
    } else {
      if ( isset( $options['page'] )) {
        $this->page = $options['page'];
      }

      if ( isset( $options['per_page'] )) {
        $this->per_page = $options['per_page'];
      }
    }

    // store total pages
    if ( empty( $this->total ) ) {
      $this->pages = 0;
    } else {
      $this->pages = intval( ceil( $this->total / $this->per_page ));

      // store range
      $start = ( $this->page - 1 ) * $this->per_page + 1;
      $end = $this->page * $this->per_page;

      $this->range = [
        $start
      , $end
      , 'start' => $start
      , 'end' => $end
      ];
    }

    // store next
    if ( $this->page < $this->pages ) {
      $this->next = $this->page + 1;
    }

    // store previous
    if ( $this->page > 1 ) {
      $this->previous = $this->page - 1;
    }

    // store necessary
    $this->necessary = $this->pages > 1;
  }


  // Convert to array
  public function toArray() {
    return [
     'total' => $this->total
    , 'pages' => $this->pages
    , 'range' => $this->range
    , 'next' => $this->next
    , 'previous' => $this->previous
    , 'necessary' => $this->necessary
    , 'page' => $this->page
    , 'per_page' => $this->per_page
    ];
  }

}


// Exceptions
class PaginateItemsNotAcceptableException extends Exception {}

