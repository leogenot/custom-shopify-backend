<?php

/**
 * Storefront for Craft CMS
 *
 */

namespace leo\storefront\services;

use craft\base\Component;
use leo\storefront\enums\ShopifyType;
use leo\storefront\helpers\CacheHelper;

/**
 * Class OrdersService
 *
 * @author  leo
 * @package leo\storefront\services
 */
class OrdersService extends Component {

    public function upsert(array $data) {
        // Clear the caches for all products in the order to ensure our stock
        // is up-to-date
        foreach ($data['line_items'] as $item)
            if ($item['product_exists'])
                CacheHelper::clearCachesByShopifyId($item['product_id'], ShopifyType::Product);

        CacheHelper::clearCachesByShopifyId(
            $data['customer']['id'],
            ShopifyType::Customer
        );
    }
}
