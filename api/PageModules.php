<?php

namespace api;

use craft\base\Element;
use utils\Assets;
use utils\Error;
use utils\Fields;
use utils\Str;

function parseCustomLink($link) {
  $links = $link->linkFields ? $link->linkFields->all() : null;

  return [
    'tagLeft' => $link->tagLeft,
    'tagRight' => $link->tagRight,
    'headline' => $link->headline,
    'text' => $link->text,
    'media' => Assets::get_one($link->media),
    'link' => Fields::link_field($link->linkField),
    'links' => $links ? array_map(function ($item) {
      return Fields::link_field($item->linkField);
    }, $link->linkFields->all()) : null
  ];
}

class PageModules {
  private static function meta($block) {
    return [
      'settings' => []
    ];
  }

  // loop all page modules
  public static function get_all(Element $entry) {
    if (empty($entry->pageModules)) return null;

    $blocks = $entry->pageModules->all();

    return array_map(function ($block) {
      $handle = $block->type->handle;

      return self::get_one($handle, $block);
    }, $blocks);
  }

  // pass each module to its model
  public static function get_one(String $handle, Object $block) {
    $name = Str::to_snake($handle);

    return [
      'id' => $block->id ?? null,
      'type' => $handle,
      'data' => method_exists(new PageModules(), $name)
        // ? array_merge(self::meta($block), self::{$name}($block))
        ? self::{$name}($block)
        : Error::no_model('PageModules', $handle, $block),
    ];
  }

  // module models

  private static function homepage_hero(Object $block) {
    if (empty($block)) return null;

    return [
      'responsiveMedia' => Assets::get_responsive($block->responsiveMedia),
      'tag' => $block->tag,
      'headline' => $block->headline,
      'icon' => Assets::get_one($block->icon),
      'announcementContent' => Fields::redactor($block->announcementContent),
      'announcementLink' => Fields::redactor($block->announcementLink),
      'announcementClosable' => $block->announcementClosable,
      'darkText' => $block->textColor, // false === 'light'
      'badgeImage' => Assets::get_one($block->badgeImage),
      'badgeLink' => Fields::link_field($block->badgeLink),
    ];
  }

  private static function page_hero(Object $block) {
    if (empty($block)) return null;

    return [
      'tag' => $block->tag,
      'headline' => $block->headline,
      'media' => Assets::get_one($block->media),
      'imageOverlay' => $block->layout
    ];
  }

  public static function headline_module($block) {
    if (empty($block)) return null;

    return [
      'tag' => $block->tag,
      'headline' => $block->headline,
      'link' => Fields::link_field($block->linkField),
      'textfield' => Fields::redactor($block->textfield),
    ];
  }

  private static function content_module(Object $block) {
    if (empty($block)) return null;

    return [
      'richtext' => Fields::redactor($block->richtext),
      'media' => Assets::get_one($block->media),
      'reversed' => $block->reversed,
      'showImageCaption' => $block->showImageCaption
    ];
  }

  private static function list_module(Object $block) {
    if (empty($block)) return null;

    $rows = $block->items->all();

    if (empty($rows)) return null;

    $items = array_map(function ($item) {
      $entry = $item->entry->one();

      if (!empty($entry)) return Entries::get_one($entry, true);

      $customLink = $item->customLink->one();

      if (!empty($customLink)) return parseCustomLink($customLink);

      return null;
    }, $rows);

    return [
      'items' => $items,
      'reversed' => $block->reversed,
    ];
  }

  private static function dual_teaser(Object $block) {
    if (empty($block)) return null;

    $rows = $block->items->all();

    if (empty($rows)) return null;

    $items = array_map(function ($item) {
      return [
        'tag' => $item->tag,
        'headline' => $item->headline,
        'media' => Assets::get_one($item->media),
        'link' => Fields::link_field($item->linkField)
      ];
    }, $rows);

    return [
      'items' => $items,
    ];
  }

  private static function text_teaser(Object $block) {
    if (empty($block)) return null;

    $rows = $block->items->all();

    if (empty($rows)) return null;

    $items = array_map(function ($item) {
      return [
        'medias' => Assets::get_many($item->medias),
        'link' => Fields::link_field($item->linkField)
      ];
    }, $rows);

    return [
      'items' => $items,
    ];
  }

  public static function card_carousel($block) {
    if (empty($block)) return null;

    $rows = $block->items->all();

    if (empty($rows)) return null;

    $items = array_map(function ($item) {
      $entry = $item->entry->one();

      if (!empty($entry)) return Entries::get_one($entry, true);

      $customLink = $item->customLink->one();

      if (!empty($customLink)) return parseCustomLink($customLink);

      return null;
    }, $rows);

    return [
      'items' => $items,
    ];
  }

  public static function rooms_carousel($block) {
    if (empty($block)) return null;

    return [
      'items' => Entries::get_many($block->entries->all(), true),
    ];
  }

  public static function rooms_grid($block) {
    if (empty($block)) return null;

    return [
      'items' => Entries::get_many($block->entries->all(), true),
    ];
  }

  public static function fullscreen_media($block) {
    if (empty($block)) return null;

    return [
      'responsiveMedia' => Assets::get_responsive($block->responsiveMedia),
      'showImageCaption' => $block->showImageCaption
    ];
  }

  public static function entry_carousel($block) {
    if (empty($block)) return null;

    $rows = $block->items->all();

    $items = array_map(function ($row) {
      return [
        'tag' => $row->tag,
        'headline' => $row->headline,
        'media' => Assets::get_one($row->media),
        'link' => Fields::link_field($row->linkField)
      ];
    }, $rows);

    return [
      'items' => $items,
    ];
  }

  public static function image_teaser($block) {
    if (empty($block)) return null;

    return [
      'tag' => $block->tag,
      'headline' => $block->headline,
      'text' => Fields::redactor($block->text),
      'link' => Fields::link_field($block->linkField),
      'media' => Assets::get_one($block->media),
    ];
  }

  private static function gallery(Object $block) {
    if (empty($block)) return null;

    return [
      'columns' => [
        'left' => Assets::get_many($block->columns->left),
        'right' => Assets::get_many($block->columns->right),
      ],
      'showImageCaptions' => $block->showImageCaptions
    ];
  }

  private static function bullets(Object $block) {
    if (empty($block)) return null;

    $bullets = $block->items->all();

    $items = array_map(function ($item) {
      return [
        'title' => $item->title
      ];
    }, $bullets);


    return [
      'headline' => $block->headline,
      'items' => $items,
    ];
  }

  private static function all_news_events(Object $block) {
    if (empty($block)) return null;
    return [];
  }

  private static function all_offers_packages(Object $block) {
    if (empty($block)) return null;
    return [];
  }

  private static function all_press(Object $block) {
    if (empty($block)) return null;
    return [];
  }
}
