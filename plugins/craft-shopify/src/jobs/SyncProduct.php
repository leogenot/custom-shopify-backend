<?php

/**
 * craft-shopify module for Craft CMS 4.x
 *
 * @link      https://leocompany.com
 * @copyright Copyright (c) 2021 One Design Company
 */


namespace leo\craftshopify\jobs;


use Craft;
use craft\helpers\Json;
use craft\queue\BaseJob;
use Exception;
use leo\craftshopify\CraftShopify;
use leo\craftshopify\elements\Product;
use craft\elements\Entry;

/**
 * @author    One Design Company
 * @package   craft-shopify
 * @since     1.0.0
 */
class SyncProduct extends BaseJob {
    /**
     * @var array
     */
    public $productData = null;

    /**
     * @inheritdoc
     */
    public function execute($queue): void {
        if (!$this->productData) {
            throw new Exception('Product Data is required.');
        }

        $shopifyId = $this->productData['id'];
        $shopifyHandle = $this->productData['handle'];
        $entryWithId = Entry::find()->section('products')->shopifyId([$shopifyId])->one();
        $entryWithHandle = Entry::find()->section('products')->slug([$shopifyHandle])->one();

        if (!$entryWithId && !$entryWithHandle) {
            $product = CraftShopify::$plugin->product->getProductModel($shopifyId);
            CraftShopify::$plugin->product->populateProductModel($product, $this->productData);
            $entry = CraftShopify::$plugin->product->updateEntry($this->productData);
            Craft::$app->getElements()->saveElement($product);
            $queue->setProgress(100);
        }

        if ($entryWithId) {
            $product = CraftShopify::$plugin->product->getProductModel($shopifyId);
            CraftShopify::$plugin->product->populateProductModel($product, $this->productData);
            $entry = CraftShopify::$plugin->product->updateEntry($this->productData);
            Craft::$app->getElements()->saveElement($product);
            $queue->setProgress(100);
            if ($product->getErrors()) {
                throw new Exception('Product ' . $shopifyId . ' - ' . Json::encode($product->getErrors()));
            }
        } else {
            $entry = Entry::find()->section('products')->slug([$shopifyHandle])->one();
            if (!$entry) {
                throw new Exception('Unknown error saving product ' . $shopifyId);
            }
            $entry->setFieldValue('shopifyId', (string)$shopifyId);
            Craft::$app->getElements()->saveElement($entry);
            $queue->setProgress(100);
        }
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string {
        return Craft::t('craft-shopify', "Sync Product ID {$this->productData['id']} from Shopify");
    }
}
