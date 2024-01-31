<?php

/**
 * Storefront for Craft CMS
 *
 */

namespace leo\storefront\controllers;

use craft\errors\ElementNotFoundException;
use craft\web\Controller;
use leo\storefront\Storefront;
use Throwable;
use yii\db\Exception;
use yii\web\BadRequestHttpException;

/**
 * Class HooksController
 *
 * @author  leo 
 * @package leo\storefront\controllers
 */
class HooksController extends Controller {

    protected $allowAnonymous = true;
    public $enableCsrfValidation = false;

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function actionListen() {
        Storefront::getInstance()->webhook->listen();

        return '';
    }
}
