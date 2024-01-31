<?php

/**
 * Storefront for Craft CMS
 *
 */

namespace leo\storefront\controllers;

use Craft;
use craft\errors\MissingComponentException;
use craft\web\Controller;
use leo\storefront\jobs\ImportProductsJob;
use leo\storefront\Storefront;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class SetupController
 *
 * @author  leo 
 * @package leo\storefront\controllers
 */
class SetupController extends Controller {

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     * @throws Exception
     */
    public function actionWebhooks() {
        Storefront::getInstance()->webhook->install();
        return $this->redirectToPostedUrl();
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     */
    public function actionImport() {
        Craft::$app->getQueue()->push(new ImportProductsJob());
        Craft::$app->getSession()->setNotice('Queued import task');
        return $this->redirectToPostedUrl();
    }
}
