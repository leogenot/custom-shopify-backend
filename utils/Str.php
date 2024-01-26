<?php

namespace utils;

/**
 * some string-helpers
 */

class Str {
  // camelCase -> kebab-case
  public static function to_kebab(String $string) {
    return strtolower(preg_replace('%([a-z])([A-Z])%', '\1-\2', $string));
  }

  // camelCase -> snake_case
  public static function to_snake(String $string) {
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
  }

  // <kebab-case | snake_case> -> camelCase
  public static function to_camel(String $string, $separator = '-') {
    return str_replace($separator, '', ucwords($string, $separator));
  }
}
