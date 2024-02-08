<?php

/**
 * craft-shopify module for Craft CMS 3.x
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
        $product = CraftShopify::$plugin->product->getProductModel($shopifyId);
        CraftShopify::$plugin->product->populateProductModel($product, $this->productData);

        if (Craft::$app->getElements()->saveElement($product)) {
            $queue->setProgress(100);
        } else {

            throw new Exception('Unknown error saving product ' . $product->id);
        }

        if ($product->getErrors()) {
            throw new Exception('Product ' . $product->id . ' - ' . Json::encode($product->getErrors()));
        }
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string {
        return Craft::t('craft-shopify', "Sync Product ID {$this->productData['id']} from Shopify");
    }
}