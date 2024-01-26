<?php

namespace api;

use craft\elements\Entry;
use utils\Assets;
use utils\Fields;

class Menus {
  public static function menu_item(Entry $entry) {
    $children = array_map(function ($child) {
      return self::menu_item($child);
    }, $entry->children->all());

    $link = Fields::link_field($entry->linkField);

    return [
      'id' => (int) $entry->id,
      ...($link ? $link : [
        'url' => $entry->urlField
      ]),
      'media' => Assets::get_one($entry->image),
      'children' => $entry->hasDescendants ? $children : null,
    ];
  }
}
