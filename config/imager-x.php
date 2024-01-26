<?php

return [
	'*' => [
		'transformer' => getenv('IMGIX_ENABLED') == 'true' ? 'imgix' : 'craft',
		'imgixProfile' => 'default',
		'imgixConfig' => [
			'default' => [
				'domain' => getenv('IMGIX_DOMAIN'),
				'useHttps' => true,
				'signKey' => '',
				'sourceIsWebProxy' => false,
				'useCloudSourcePath' => true,
				'getExternalImageDimensions' => true,
				'addPath' => [
					'defaulter' => 'uploads'
				],
				'defaultParams' => [
					'auto' => 'compress,format',
					'q' => 75
				],
			],
		],
		'noop' => getenv('IMGIX_ENABLED') == 'false',
		'imagerSystemPath' => '@webroot/media/',
		'imagerUrl' => '@web/media/',
		'cacheEnabled' => true,
		'cacheDuration' => 1209600, // 14 days
		'cacheDurationRemoteFiles' => 1209600, // 14 days
		'jpegQuality' => 75,
	],
];
