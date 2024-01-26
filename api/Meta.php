<?php

namespace api;

use utils\Url;

/**
 * meta info about entries,
 * typically sent along the rest of content for all api endpoints
 */

class Meta {
  public static function get_meta($entry) {
    if (empty($entry)) return null;

    return [
      'meta' => [
        'id' => (int) $entry->id,
        'title' => $entry->title,
        'slug' => $entry->slug,
        'url' => Url::parse($entry->url),
        'section' => $entry->section->handle,
        'postDate' => $entry->postDate->format('Y-m-d\\TH'),
        'dateUpdated' => $entry->dateUpdated->format('Y-m-d\\TH'),
      ]
    ];
  }
}
