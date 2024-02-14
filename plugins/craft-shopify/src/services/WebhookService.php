<?php

/**
 * craft-shopify-cms module for Craft CMS 4.x
 *

 * 
 */


namespace leo\craftshopify\services;


use craft\base\Component;
use craft\helpers\Queue;
use craft\helpers\StringHelper;
use leo\craftshopify\jobs\PurgeWebhookResponses;
use leo\craftshopify\models\WebhookResponse;
use leo\craftshopify\records\WebhookResponseRecord;
use Throwable;

class WebhookService extends Component {

    /**
     * @param WebhookResponse $request
     * @return bool
     * @throws \Exception
     */
    public function saveResponse(WebhookResponse $request) {
        $record = new WebhookResponseRecord();
        $record->payload = $request->payload;
        $record->errors = $request->errors;
        $record->uid = StringHelper::UUID();
        $record->type = $request->topic;
        $record->webhookId = $request->webhookId;

        return $record->save();
    }

    /**
     * Delete webhook responses older than X days
     *
     * @param int $olderThan
     * @throws Throwable
     */
    public function purgeResponses(int $olderThan = 30) {
        Queue::push(new PurgeWebhookResponses([
            'olderThan' => $olderThan
        ]));
    }
}
