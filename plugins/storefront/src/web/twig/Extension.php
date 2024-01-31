<?php

/**
 * Storefront for Craft CMS
 *
 */

namespace leo\storefront\web\twig;

use leo\storefront\Storefront;
use leo\storefront\web\twig\tokenparsers\ShopifyTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Class Extension
 *
 * @author  leo 
 * @package leo\storefront\web\twig
 */
class Extension extends AbstractExtension implements GlobalsInterface {

    public function getTokenParsers() {
        return [
            new ShopifyTokenParser(),
        ];
    }

    public function getGlobals(): array {
        return [
            'currentCustomerId' => Storefront::getInstance()->customers->getCustomerId(),
        ];
    }
}
