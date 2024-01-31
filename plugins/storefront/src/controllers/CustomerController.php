<?php

/**
 * Storefront for Craft CMS
 *
 */

namespace leo\storefront\controllers;

use Craft;
use craft\web\Controller;
use leo\storefront\Storefront;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class CustomerController
 *
 * @author  leo 
 * @package leo\storefront\controllers
 */
class CustomerController extends Controller {

    protected $allowAnonymous = true;

    /**
     * Log the user in to their Shopify account
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionLogin() {
        $request = Craft::$app->getRequest();

        $email = $request->getRequiredBodyParam('email');
        $password = $request->getRequiredBodyParam('password');

        if ($errors = Storefront::getInstance()->customers->login($email, $password))
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => ['errors' => $errors],
            ]);

        return $this->redirectToPostedUrl();
    }

    /**
     * Log the user out
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionLogout() {
        Storefront::getInstance()->customers->logout();

        return $this->redirectToPostedUrl();
    }
}
