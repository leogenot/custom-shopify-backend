<?php

namespace leo\craftshopify\jobs;

use Craft;
use craft\helpers\Json;
use craft\queue\BaseJob;
use Exception;
use leo\craftshopify\CraftShopify;
use craft\elements\Entry;

class SyncProduct extends BaseJob {
    public $productData = null;

    public function execute($queue): void {
        if (!$this->productData) {
            throw new Exception('Product Data is required.');
        }

        $shopifyId = $this->productData['id'];
        $shopifyHandle = $this->productData['handle'];

        // Get the Craft entry by handle
        // $entry = Craft::$app->getEntries()->getEntryByHandle($shopifyHandle);
        $entry = Entry::find()->section('products')->slug([$shopifyHandle])->one();

        if (!$entry) {
            throw new Exception('Craft entry with handle ' . gettype($shopifyId) . ' not found.');
        }

        // Update the Shopify ID field in the Craft entry
        $entry->setFieldValue('shopifyId', (string)$shopifyId);

        // Save the Craft entry
        if (Craft::$app->getElements()->saveElement($entry)) {
            $queue->setProgress(100);
        } else {
            throw new Exception('Unknown error saving product ' . $shopifyId);
        }
    }

    protected function defaultDescription(): string {
        return Craft::t('craft-shopify', "Sync Product ID {$this->productData['id']} from Shopify");
    }
}
