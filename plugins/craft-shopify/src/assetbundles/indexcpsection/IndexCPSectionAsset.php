<?php

/**
 * Craft Shopify plugin for Craft CMS 4.x
 *
 * Bring Shopify products into Craft
 *
 * @link      https://leocompany.com/
 * @copyright Copyright (c) 2021 One Design Company
 */

namespace leo\craftshopify\assetbundles\indexcpsection;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    One Design Company
 * @package   CraftShopify
 * @since     1.0.0
 */
class IndexCPSectionAsset extends AssetBundle {
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init() {
        $this->sourcePath = "@leo/craftshopify/assetbundles/indexcpsection/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Index.js',
        ];

        $this->css = [
            'css/Index.css',
        ];

        parent::init();
    }
}
