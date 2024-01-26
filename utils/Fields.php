<?php

namespace utils;

use Craft;
use utils\Url;

/**
 * some fields-helpers,
 * parses different cms fields
 */

class Fields {
  public static function link_field($linkField) {
    if (empty($linkField)) return null;

    $title = $linkField->getText();
    $url = $linkField->getUrl();
    $target = $linkField->getTarget();
    $type = $linkField->getType();

    if (empty($url) && $title === 'Learn More') return null;

    $appendedHTTPS = $type === 'custom' && str_starts_with($url, 'www') ? 'https://' . $url : $url;

    return [
      'title' => $title,
      'url' => Url::parse($appendedHTTPS),
      'target' => $target,
      'type' => $type
    ];
  }

  public static function redactor($richtext) {
    if (empty($richtext)) return null;

    // Parse Redactor content and add custom data to links
    $dom = new \DOMDocument();
    $dom->loadHTML(mb_convert_encoding($richtext, 'HTML-ENTITIES', 'UTF-8'));
    $links = $dom->getElementsByTagName('a');

    if ($links && $links->length > 0) {

      foreach ($links as $link) {
        $href = $link->getAttribute('href');

        if (strpos($href, '{entry') !== false && strpos($href, 'url}') !== false) {
          $reference = explode(':', $href);
          $entryId = (int) $reference[1];
          $element = Craft::$app->entries->getEntryById($entryId);
        } else {

          if (strpos($href, '/uploads') == false) {
            $uri = str_replace(getenv('DEFAULT_SITE_URL'), '', $link->getAttribute('href'));
            $link->setAttribute('href', Url::parse($uri));
            $element = Craft::$app->elements->getElementByUri($uri);
          }
        }
      }
    }

    $html = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $dom->saveHTML());
    return $html;

    return $richtext;
  }

  public static function radio($radioButtons, $defaultValue) {
    if (empty($radioButtons)) return null;

    return $radioButtons->value ? $radioButtons->value : $defaultValue;
  }

  public static function category($field, $getMany = false) {
    if (empty($field)) return null;

    if ($getMany) {
      $items = $field->all();

      return array_map(function ($item) {
        return empty($item) ? null : $item->title;
      }, $items);
    }

    $item = $field->one();

    return empty($item) ? null : $item->title;
  }

  public static function date($field) {
    // needs to be defined in CMS with the "Date" and/or "Date 2" field(s)

    if (empty($field)) return null;
    if (empty($field->date) && empty($field->date2)) return null;

    return [
      'type' => 'date',
      'from' => !empty($field->date) ? $field->date->format('d.m.Y') : null,
      'to' => !empty($field->date2) ? $field->date2->format('d.m.Y') : null
    ];
  }
}
