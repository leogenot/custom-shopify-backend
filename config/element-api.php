<?php

use craft\web\Request;

use craft\elements\Entry;
use craft\elements\GlobalSet;

use api\Entries;
use api\Globals;
use api\Menus;

use utils\Str;

$menus = ['mainMenu', 'footerMenu', 'socialMediaMenu'];

return [
  'defaults' => [
    'elementType' => Entry::class,
    'cache' => (new Request)->getToken() !== null || getenv('DEV_MODE') === 'true' ? false : true,
    'paginate' => false,
    'one' => true,
  ],

  'endpoints' => [
    // cms-entry by id or slug
    // /entry/123 or /entry/the-slug
    'api/entry/<slug:{slug}>.json' => function ($slug) use ($menus) {
      $handle = is_numeric($slug) ? 'id' : 'slug';

      return [
        'criteria' => [
          $handle => $slug,
          'site' => Craft::$app->request->getQueryParam('locale', 'en'),
          'section' => ['not', ...$menus]
        ],
        'transformer' => function (Entry $entry) {
          return Entries::get_one($entry, false);
        },
      ];
    },

    'api/error.json' => function () {
      return [
        'criteria' => [
          'slug' => 'error-page',
          'site' => Craft::$app->request->getQueryParam('locale', 'en'),
        ],
        'transformer' => function (Entry $entry) {
          return Entries::get_one($entry, false);
        },
      ];
    },

    // list of entries by entry->section
    // entries.json will check all Channel sections
    // returns meta and teaser data for each entry
    // entries.json?section=article&genre=123 returns articles with genre 123
    // search for entries using query param 'q'
    'api/entries.json' => function () use ($menus) {
      // get entries that's not yet published
      $drafts = Craft::$app->request->getQueryParam('drafts');
      // limit entries to a/many specific cms-section
      $request = Craft::$app->request->getQueryParam('section');
      // limit entries to a/many specific cms-section-type
      $entryType = Craft::$app->request->getQueryParam('type');
      // limit response to a number on entries
      $amount = Craft::$app->request->getParam('amount');
      // get specific entries based on id
      $ids = Craft::$app->request->getParam('id');

      // find entries in certain entry->section(s)
      $section = empty($request)
        ? ['articlePage', 'contentPage']
        : Str::to_camel($request);

      $criteria = [
        'section' => $section,
        'type' => $entryType,
        'id' => isset($ids) ? $ids : null,
        'orderBy' => 'postDate desc',
        'drafts' => isset($drafts),
        // 'search' => Helpers::use_search_param('q')
      ];

      return [
        'criteria' => $criteria,
        'one' => false,
        'paginate' => true,
        'elementsPerPage' => $amount ? intval($amount) : 20,
        'transformer' => function (Entry $entry) {
          return Entries::get_one($entry, true);
        },
      ];
    },

    // menu items by kebab-cased menu section,
    // /menu/footer-menu returns entries in footerMenu
    'api/menu/<handle:{slug}>.json' => function ($handle) {
      $section = Str::to_camel($handle);

      return [
        'criteria' => [
          'section' => $section,
          'level' => 1, // children is collected in transformer function
          'site' => Craft::$app->request->getQueryParam('locale', 'en'),
        ],
        'one' => false,
        'transformer' => function (Entry $entry) {
          return Menus::menu_item($entry);
        },
      ];
    },

    // cms-globals by handle
    // /global/contacts-list returns the contactList globals
    'api/global/<slug:{slug}>.json' => function ($slug) {
      $handle = Str::to_camel($slug);

      return [
        'criteria' => [
          'handle' => $handle,
          'site' => Craft::$app->request->getQueryParam('locale', 'en'),
        ],
        'elementType' => GlobalSet::class,
        'transformer' => function (GlobalSet $globalSet) {
          return Globals::get_one($globalSet);
        },
      ];
    },


    'api/ttfb.json' => function () {
      return [
        'one' => true,
        'transformer' => function () {
          return [
            'status' => 200,
            'message' => 'this is the fastest possible response from the server'
          ];
        },
      ];
    },
  ]
];
