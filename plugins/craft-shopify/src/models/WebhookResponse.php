<?php

/**
 * craft-shopify-cms module for Craft CMS 4.x
 *

 * 
 */


namespace leo\craftshopify\models;


use craft\base\Model;

class WebhookResponse extends Model {
    /**
     * @var int|null ID
     */
    public $id;

    /**
     * @var string|null
     */
    public $topic;

    /**
     * @var string|null
     */
    public $uid;

    /**
     * @var string|null
     */
    public $webhookId;

    /**
     * @var string|null
     */
    public $payload;

    /**
     * @var string|null
     */
    public $errors;
}
