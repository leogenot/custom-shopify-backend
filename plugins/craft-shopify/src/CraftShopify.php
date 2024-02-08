<?php

namespace leo\craftshopify;

use Craft;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\elements\Entry;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\ElementHelper;
use craft\helpers\UrlHelper;
use craft\services\Elements;
use craft\services\Fields;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use leo\craftshopify\elements\Product;
use leo\craftshopify\models\Settings;
use leo\craftshopify\services\ProductService;
use leo\craftshopify\services\ShopifyService;
use leo\craftshopify\services\WebhookService;
use leo\craftshopify\utilities\CraftShopifyUtility as CraftShopifyUtilityUtility;
use yii\base\Event;
use yii\base\ModelEvent;

/**
 * Craft Shopify plugin
 *
 * @method static Plugin getInstance()
 * @method Settings getSettings()
 */
class CraftShopify extends Plugin {
    public static CraftShopify $plugin;
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;
    public bool $hasCpSection = true;

    public function init(): void {
        parent::init();
        self::$plugin = $this;
        // Defer most setup tasks until Craft is fully initialized
        $this->setComponents([
            'shopify' => ShopifyService::class,
            'product' => ProductService::class,
            'webhook' => WebhookService::class
        ]);

        Craft::$app->projectConfig
            ->onAdd(ProductService::CONFIG_PRODUCT_FIELDLAYOUT_KEY, [$this->product, 'handleChangedFieldLayout'])
            ->onUpdate(ProductService::CONFIG_PRODUCT_FIELDLAYOUT_KEY, [$this->product, 'handleChangedFieldLayout'])
            ->onRemove(ProductService::CONFIG_PRODUCT_FIELDLAYOUT_KEY, [$this->product, 'handleChangedContactFieldLayout']);

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'leo\craftshopify\console\controllers';
        }

        Event::on(
            Entry::class,
            Entry::EVENT_AFTER_SAVE,
            function (ModelEvent $event) {
                /** @var Entry $entry */
                $entry = $event->sender;

                if (ElementHelper::isDraftOrRevision($entry)) {
                    return;
                }

                CraftShopify::$plugin->product->updateRelatedProducts($entry);
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['webhook'] = 'craft-shopify/webhook/index';
            }
        );


        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['craft-shopify'] = 'craft-shopify/default';
                $event->rules['craft-shopify/settings/shopify'] = 'craft-shopify/settings/shopify';
            }
        );

        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Product::class;
            }
        );


        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = CraftShopifyUtilityUtility::class;
            }
        );


        Craft::info(
            Craft::t(
                'craft-shopify',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    public function getCpNavItem(): array {
        $item = parent::getCpNavItem();
        $item['subnav'] = [
            'utilities' => [
                'label' => 'Utilities',
                'url' => UrlHelper::cpUrl('utilities/craft-shopify')
            ],
        ];

        if (Craft::$app->config->general->allowAdminChanges) {
            $item['subnav']['settings'] = [
                'label' => 'Settings',
                'url' => 'craft-shopify/settings'
            ];
        }

        return $item;
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Settings {
        return new Settings();
    }

    /**
     * Slightly more complex settings
     *
     * @inheritdoc
     */
    public function getSettingsResponse(): mixed {
        $url = UrlHelper::cpUrl('craft-shopify/settings');
        return Craft::$app->getResponse()->redirect($url);
    }
}
