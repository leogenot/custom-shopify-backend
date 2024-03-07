<?php

/**
 * Craft Shopify plugin for Craft CMS 4.x
 *
 * Bring Shopify products into Craft
 *
 * 
 */

namespace leo\craftshopify\utilities;

use DateTime;
use leo\craftshopify\CraftShopify;
use leo\craftshopify\assetbundles\craftshopifyutilityutility\CraftShopifyUtilityUtilityAsset;

use Craft;
use craft\base\Utility;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use utils\Str;

/**
 * Craft Shopify Utility
 *

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

    public static function createSection($sectionName): void {

        $section = new Section([
            'name' => $sectionName,
            'handle' => Str::to_camel($sectionName),
            'type' => Section::TYPE_CHANNEL,
            'siteSettings' => [
                new Section_SiteSettings([
                    'siteId' => Craft::$app->sites->getPrimarySite()->id,
                    'enabledByDefault' => true,
                    'hasUrls' => true,
                    'uriFormat' => Str::to_camel($sectionName) . '/{slug}',
                    'template' => Str::to_camel($sectionName) . '/_entry',
                ]),
            ]
        ]);

        try {
            $success = Craft::$app->sections->saveSection($section);
        } catch (\craft\errors\EntryTypeNotFoundException $e) {
            die("<pre>" . var_export("From \\craft\\errors\\EntryTypeNotFoundException: {$e->getMessage()}", true) . "</pre>");
        } catch (\Throwable $e) {
            die("<pre>" . var_export("From \\Throwable: {$e->getMessage()}", true) . "</pre>");
        } finally {
            die("<pre>" . var_export($success, true) . "</pre>");
        }
    }
}
