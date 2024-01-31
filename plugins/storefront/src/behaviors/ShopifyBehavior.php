<?php

/**
 * Storefront for Craft CMS
 *
 */

namespace leo\storefront\behaviors;

use yii\base\Behavior;

/**
 * Class ShopifyBehavior
 *
 * @author  leo 
 * @package leo\storefront\behaviors
 */
class ShopifyBehavior extends Behavior {

    // Static
    // =========================================================================

    public static $fieldHandles = [
        'shopifyId' => true,
    ];

    // Properties
    // =========================================================================
    // Note: These fields are populated by Storefront::onBeforeElementQueryPrepare()

    /** @var string The products Shopify ID */
    public $shopifyId;
}
