<?php

namespace api;

use craft\elements\GlobalSet;

use utils\Assets;
use utils\Str;
use utils\Error;
use utils\Fields;

class Globals {
  private static function meta(GlobalSet $globalSet) {
    return [
      'meta' => [
        'id' => $globalSet->id,
      ],
    ];
  }

  public static function get_one(?GlobalSet $globalSet) {
    if (!isset($globalSet)) return null;

    $handle = $globalSet->handle;
    $name = Str::to_snake($handle);

    return method_exists(new Globals(), $name)
      ? array_merge(self::meta($globalSet), self::{$name}($globalSet))
      : Error::no_model('Globals', $handle, $globalSet);
  }

  // global models
  // name is snake_case version of global-handle in cms

  private static function settings(GlobalSet $globalSet) {
    $links = array_map(function ($item) {
      return [
        'image' => Assets::get_one($item->image),
        'link' => Fields::link_field($item->linkField)
      ];
    }, $globalSet->imageLinks->all());

    return [
      'faviconDark' => Assets::get_one($globalSet->image),
      'faviconLight' => Assets::get_one($globalSet->image2),
      'siteImage' => Assets::get_one($globalSet->image3),
      'siteHeaderContent' => Fields::redactor($globalSet->richtextHeader),
      'siteHeaderImageLinks' => $links
    ];
  }

  private static function loading_screens(GlobalSet $globalSet) {

    return [
      'imageOne' => Assets::get_one($globalSet->image),
      'imageTwo' => Assets::get_one($globalSet->image2)
    ];
  }

  private static function urls(GlobalSet $globalSet) {

    return [
      'booking' => Fields::link_field($globalSet->linkField),
      'newsletter' => Fields::link_field($globalSet->linkField2),
    ];
  }

  private static function danger_zone(GlobalSet $globalSet) {

    return [
      'enableSite' => $globalSet->lightswitch,
      'placeholder' => Entries::get_one($globalSet->entries->one())
    ];
  }

  private static function footer(GlobalSet $globalSet) {
    return [
      'image' => Assets::get_one($globalSet->image),
      'image2' => Assets::get_one($globalSet->image2),
      'richtext' => Fields::redactor($globalSet->richtext),
    ];
  }

  private static function header(GlobalSet $globalSet) {
    return [
      'content' => 'This is the header content'
    ];
  }

  private static function language(GlobalSet $globalSet) {
    $output = [];

    foreach ($globalSet->languageField as $row) {
      $output[$row['handleHandle']] = $row['label'];
    }

    return [
      ...$output,
    ];
  }
}
