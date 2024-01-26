<?php

namespace utils;

/**
 * some url helpers
 */

class Url {
  public static function parse($url) {
    if (empty($url)) return null;

    $splitted = is_array($url) ? implode('/', $url) : $url;
    $parsed = parse_url($splitted);

    if (isset($parsed['scheme']) && in_array($parsed['scheme'], ['mailto'])) {
      return $url;
    }

    $isRelative = 0;

    (string) $stripped = str_replace([
      getenv('FRONTEND_SITE_URL'),
      getenv('CP_BASE_URL'),
    ], '', $url, $isRelative);

    $allRelativeUrls = $stripped;

    return $isRelative === 0 ? $stripped : $allRelativeUrls;
  }
}
