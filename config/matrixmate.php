<?php

$defaultConfig = [
  'hideUngroupedTypes' => true,
  'defaultTabFirst' => true,
  'groups' => [
    [
      'label' => 'Modules',
      'types' => [
        'headlineModule',
        'contentModule',
        'fullscreenMedia',
        'listModule',
        'dualTeaser',
        'textTeaser',
        'cardCarousel',
        'roomsCarousel',
        'entryCarousel',
        'gallery',
        'bullets',
        'imageTeaser'
      ]
    ]
  ],
  'types' => [
    'homepageHero' => [
      'tabs' => [
        [
          'label' => 'Announcement',
          'fields' => [
            'announcementContent',
            'announcementLink',
            'announcementClosable'
          ]
        ],
        [
          'label' => 'Badge',
          'fields' => [
            'badgeImage',
            'badgeLink',
          ]
        ],
      ]
    ],
    'roomsGrid' => [
      'maxLimit' => 1
    ],
    'allNewsEvents' => [
      'maxLimit' => 1
    ],
    'allOffersPackages' => [
      'maxLimit' => 1
    ],
    'allPress' => [
      'maxLimit' => 1
    ],
  ]
];

return [
  'fields' => [
    'pageModules' => [
      '*' => $defaultConfig,

      'section:index' => array_merge_recursive($defaultConfig, [
        'groups' => [
          'Specials' => ['homepageHero']
        ]
      ]),

      'section:contentPages' => array_merge_recursive($defaultConfig, [
        'groups' => [
          'Specials' => ['pageHero']
        ]
      ]),

      'section:roomsPage' => array_merge_recursive($defaultConfig, [
        'groups' => [
          'Specials' => ['pageHero', 'roomsGrid']
        ]
      ]),

      'section:newsEventsPage' => array_merge_recursive($defaultConfig, [
        'groups' => [
          'Specials' => ['pageHero', 'allNewsEvents']
        ]
      ]),

      'section:offersPackagesPage' => array_merge_recursive($defaultConfig, [
        'groups' => [
          'Specials' => ['pageHero', 'allOffersPackages']
        ]
      ]),

      'section:pressPage' => array_merge_recursive($defaultConfig, [
        'groups' => [
          'Specials' => ['pageHero', 'allPress']
        ]
      ]),
     
      'section:errorPage' => array_merge_recursive($defaultConfig, [
        'groups' => [
          'Specials' => ['homepageHero', 'pageHero']
        ]
      ]),
    ],
  ],
];
