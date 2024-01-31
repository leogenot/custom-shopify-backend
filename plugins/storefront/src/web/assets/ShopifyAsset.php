<?php

/**
 * Storefront for Craft CMS
 *
 */

namespace leo\storefront\web\assets;

use craft\web\AssetBundle;

/**
 * Class ShopifyAsset
 *
 * @author  leo
 * @package leo\storefront\web\assets
 */
class ShopifyAsset extends AssetBundle {

    public function init() {
        $this->sourcePath = __DIR__;

        $this->css = [
            'shopify.css',
        ];

        parent::init();
    }
}
