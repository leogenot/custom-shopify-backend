<?php

namespace utils;

use Exception;
use Craft;
use craft\helpers\Html;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use spicyweb\embeddedassets\Plugin as EmbeddedAssets;

class Assets {
  public static function get_many(?AssetQuery $query, $options = null) {
    if (empty($query)) return null;

    $assets = $query->all();

    return array_map(function ($asset) use ($options) {
      return self::get($asset, $options);
    }, $assets);
  }

  public static function get_one(?AssetQuery $query, $options = null) {
    if (empty($query)) return null;

    $asset = $query->one();

    return self::get($asset, $options);
  }

  public static function get(?Asset $asset, $options = null) {
    if (!isset($asset)) return null;

    if ($asset->getExtension() == 'svg') {
      return self::transform_svg($asset);
    }

    $type = $asset->kind;
    $transformMethod = null;

    switch ($type) {
      case 'json':
        $transformMethod = 'transform_embed';
        break;
      case 'video':
        $transformMethod = 'transform_video';
        break;

      default:
        $transformMethod = 'transform_image';
    }

    $options = isset($options) ? (object) $options : (object) [];

    $properties = self::{$transformMethod}($asset, $options, $type);

    if (!isset($properties)) return null;

    $defaultObjectFit = $asset->extension === 'png' ? 'contain' : 'cover';

    return array_merge([
      'id' => (int) $asset->id,
      'type' => $type,
      'extension' => $asset->extension,
      'focalPoint' => $asset->focalPoint,
      'objectFit' => !empty($options->objectFit) ? $options->objectFit : $defaultObjectFit
    ], $properties);
  }

  public static function transform_svg(?Asset $asset) {
    return [
      'id' => (int) $asset->id,
      'type' => 'svg',
      'code' => Html::svg($asset),
      'extension' => $asset->extension,
      'width' => (int) $asset->width,
      'height' => (int)$asset->height,
      'alt' => $asset->title,
    ];
  }

  public static function transform_image(?Asset $asset, $options, $type = 'image') {
    if (empty($asset)) return null;

    $options = isset($options) ? $options : (object)[];

    $defaults = [
      'loading' => isset($options->loading) ? $options->loading : 'lazy',
    ];

    $imagerx = Craft::$app->plugins->getPlugin('imager-x');

    $image = null;

    if ($type === 'gif') {
      $image = [$asset];
      $placeholder = null;
    } else {
      $widthParams = [
        ['width' => 2600],
        ['width' => 1880],
        ['width' => 1440],
        ['width' => 1024],
        ['width' => 796],
        ['width' => 580],
        ['width' => 320],
        ['width' => 100],
      ];
      $otherParams = [
        'q' => 35,
      ];

      try {
        $image = $imagerx->imager->transformImage($asset, $widthParams, $otherParams);
        $placeholder = $imagerx->imager->transformImage($asset, [['width' => 2]], ['q' => 0.1]);
      } catch (Exception $e) {
        return [
          'error' => $e
        ];
      }
    }

    if (!isset($image)) return [];

    return [
      'placeholder' => isset($placeholder) ? $placeholder[0]->url : null,
      'caption' => $asset->alt,
      'attributes' => array_merge($defaults, [
        'src' => $image[0]->url,
        'srcset' => $imagerx->imager->srcset($image),
        'width' => (int) $image[0]->width,
        'height' => (int) $image[0]->height,
        'alt' => $asset->alt ? $asset->alt : $asset->title,
      ])
    ];
  }

  public static function transform_video(?Asset $asset, $options) {
    if (empty($asset)) return null;

    $defaults = [
      'loading' => 'lazy',
      'controls' => false,
      'muted' => true,
      'playsInline' => true,
      'autoplay' => true,
      'loop' => true,
      'decoding' => isset($options->loading) && $options->loading === 'eager' ? 'sync' : 'async',
    ];

    return [
      'attributes' => array_merge($defaults, [
        'src' => $asset->url,
        'alt' => $asset->title,
        'width' => $asset->width,
        'height' => $asset->height,
      ])
    ];
  }

  // public static function transform_embed($asset, $options = []) {
  //   $embed = EmbeddedAssets::$plugin->methods->getEmbeddedAsset($asset);

  //   if ($embed->type === 'video') {
  //     $embed->code = $embed->getVideoCode([
  //       'autoplay=1',
  //       'controls=1',
  //       'disablekb=1',
  //       'fs=0',
  //       'loop=0',
  //       'modestbranding=1',
  //       'rel=0',
  //       'showinfo=0',
  //       'mute=0',
  //       'autohide=1'
  //     ]);
  //   }

  //   $dom = new \DOMDocument();
  //   @$dom->loadHTML($embed->code);
  //   $iframe = $dom->getElementsByTagName('iframe')[0];

  //   $width = null;
  //   $height = null;
  //   if ($iframe) {
  //     $width = $iframe->getAttribute('width') ?? null;
  //     $height = $iframe->getAttribute('height') ?? null;
  //   }

  //   return [
  //     'type' => $embed->type,
  //     'provider' => $embed->providerName,
  //     'url' => $embed->url,
  //     'title' => $embed->title,
  //     'description' => $embed->description,
  //     'code' => $embed->code,
  //     'width' => $embed->width,
  //     'height' => $embed->height,
  //     'posters' => $embed->images,
  //     'responsive' => $width == '100%' ? false : true,
  //   ];
  // }

  public static function get_responsive(?AssetQuery $query, $options = null) {
    if (empty($query)) return null;

    $assets = $query->all();

    if (count($assets) === 1) {
      return self::get($assets[0], $options);
    } else {
      // send back array of images,
      // frontend should handle this (f.ex. merge srcset).
      // as: [mobileAsset, desktopAsset]
      return [
        self::get($assets[0], $options),
        self::get($assets[1], $options),
      ];
    }
  }
}
