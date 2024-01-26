<?php

namespace utils;

// for internal server errors
// to not blow up the server if cms has an error

class Error {
  public static function no_model($className, $handle, $entry) {
    return [
      'error' => [
        'code' => 500,
        'message' => "Can't find api model for '" . $handle . "'. Create an api model function called '" . Str::to_snake($handle) . "' in /api/" . $className . ".php. Falling back to sending all entry data to client."
      ],
      'meta' => [
        'id' => (int) $entry->id,
        'slug' => $entry->slug,
      ],
      'content' => $entry,
    ];
  }

  public static function no_entry($id = 0) {
    return [
      'error' => [
        'code' => 404,
        'message' => 'Can\'t find entry with id: ' . $id
      ]
    ];
  }

  public static function no_value($message = '') {
    return [
      'error' => [
        'code' => 500,
        'message' => 'No value in the CMS for: ' . $message
      ]
    ];
  }
}
