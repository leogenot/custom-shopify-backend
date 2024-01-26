<?php

namespace api;

use craft\elements\Entry;

use api\Meta;
use utils\Assets;
use utils\Str;
use utils\Error;
use utils\Fields;

class Entries {
  public static function get_many($entries, $teaserDataOnly = false) {
    if (empty($entries)) return null;

    return array_map(function ($entry) use ($teaserDataOnly) {
      return Entries::get_one($entry, $teaserDataOnly);
    }, $entries);
  }

  public static function get_one(?Entry $entry, $teaserDataOnly = false) {
    if (!$entry) return null;

    $handle = $entry->section->handle;
    $name = Str::to_snake($handle);

    return method_exists(new Entries(), $name)
      ? array_merge(Meta::get_meta($entry, true), self::{$name}($entry, $teaserDataOnly))
      : Error::no_model('Entries', $handle, $entry);
  }

  // entry models
  // name is snake_case version of entry->section->handle in cms

  public static function index(Entry $entry) {
    return [
      'modules' => PageModules::get_all($entry)
    ];
  }

  public static function rooms_page(Entry $entry) {
    return [
      'title' => $entry->title,
      'modules' => PageModules::get_all($entry),
      // 'entries' => Entries::get_many($entry->entries->all(), true)
    ];
  }

  public static function press(Entry $entry) {
  return [
      'title' => $entry->title,
      'tag' => $entry->tag,
      'date' => Fields::date($entry),
      'media' => Assets::get_one($entry->media),
      'text' => Fields::redactor($entry->text),
      'modules' => PageModules::get_all($entry),
    ];
  }

  public static function error_page(Entry $entry) {
    return [
      'modules' => PageModules::get_all($entry)
    ];
  }

  private static function rooms(Entry $entry, $teaserDataOnly) {
    if (empty($entry)) return null;

    $hasInfo = !empty($entry->roomInformation) && count($entry->roomInformation) > 0;
    $info = $hasInfo ? (object) $entry->roomInformation[0] : null;

    return [
      'text' => $entry->textLimited,
      'richtext' => Fields::redactor($entry->richtext),
      'medias' => Assets::get_many($entry->medias),
      'essentials' => [
        'type' => $info && $info->type ? $info->type : null,
        'people' => $info && $info->people ? $info->people : null,
        'size' => $info && $info->size ? $info->size : null,
      ],

      ...($teaserDataOnly ? [] : [
        'modules' => PageModules::get_all($entry)
      ])
    ];
  }

  private static function news_events_page(Entry $entry) {
    if (empty($entry)) return null;

    return [
      'modules' => PageModules::get_all($entry)
    ];
  }

  private static function offers_packages_page(Entry $entry) {
    if (empty($entry)) return null;

    return [
      'modules' => PageModules::get_all($entry)
    ];
  }

  private static function article_pages(Entry $entry, $teaserDataOnly) {
    if (empty($entry)) return null;

    return [
      'image' => Assets::get_one($entry->image),
      'textarea' => $entry->textarea,
      'date' => Fields::date($entry),
      'entryType' => $entry->type->handle,

      ...($teaserDataOnly ? [] : [
        'modules' => PageModules::get_all($entry)
      ])
    ];
  }

  private static function content_pages(Entry $entry, $teaserDataOnly) {
    if (empty($entry)) return null;

    return [
      ...($teaserDataOnly ? [] : [
        'modules' => PageModules::get_all($entry)
      ])
    ];
  }

  private static function press_page(Entry $entry, $teaserDataOnly) {
    if (empty($entry)) return null;

    return [
      ...($teaserDataOnly ? [] : [
        'modules' => PageModules::get_all($entry)
      ])
    ];
  }
}
