<?php

/**
 * Craft Shopify plugin for Craft CMS 4.x
 *
 * Bring Shopify products into Craft
 *
 * @copyright Copyright (c) 2021 One Design Company
 */

namespace leo\craftshopify\utilities;

use DateTime;
use leo\craftshopify\CraftShopify;
use leo\craftshopify\assetbundles\craftshopifyutilityutility\CraftShopifyUtilityUtilityAsset;

use Craft;
use craft\base\Utility;

/**
 * Craft Shopify Utility
 *
 * @author    One Design Company
 * @package   CraftShopify
 * @since     1.0.0
 */
class CraftShopifyUtility extends Utility {
    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string {
        return Craft::t('craft-shopify', 'Craft Shopify');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string {
        return 'craft-shopify';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath(): string {
        return Craft::getAlias("@leo/craftshopify/assetbundles/craftshopifyutilityutility/dist/img/CraftShopifyUtility-icon.svg");
    }

    /**
     * @inheritdoc
     */
    public static function badgeCount(): int {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string {
        Craft::$app->getView()->registerAssetBundle(CraftShopifyUtilityUtilityAsset::class);

        return Craft::$app->getView()->renderTemplate('craft-shopify/_components/utilities/CraftShopify');
    }
}
