<?php

/**
 * craft-shopify module for Craft CMS 3.x
 *
 * @link      https://leocompany.com
 * @copyright Copyright (c) 2021 One Design Company
 */


namespace leo\craftshopify\controllers;


use Craft;
use craft\errors\ElementNotFoundException;
use craft\helpers\ArrayHelper;
use craft\helpers\Queue;
use craft\elements\Entry;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use craft\web\View;
use leo\craftshopify\CraftShopify;
use leo\craftshopify\elements\Product;
use leo\craftshopify\jobs\SyncProduct;
use PHPShopify\Exception\ApiException;
use PHPShopify\Exception\CurlException;
use Throwable;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @author    One Design Company
 * @package   craft-shopify
 * @since     1.0.0
 */
class ProductController extends Controller {
    /**
     * @var string[]
     */
    protected array|int|bool $allowAnonymous = [];


    /**
     * @return Response
     */
    // public function actionIndex(): Response {
    //     return $this->renderTemplate('craft-shopify/products');
    // }

    /**
     * Preps product edit variables
     *
     * @param array $variables
     * @throws NotFoundHttpException
     */
    // private function prepEditProductVariables(array &$variables) {
    //     if (empty($variables['product'])) {
    //         if (!empty($variables['productId'])) {
    //             $variables['product'] = Product::find()->id($variables['productId'])->one();

    //             if (!$variables['product']) {
    //                 throw new NotFoundHttpException('Product not found');
    //             }
    //         }
    //     }
    // }

    /**
     * Preview the Craft part of a shopify product
     *
     * @param int|null $productId
     * @return Response
     * @throws NotFoundHttpException
     */
    // public function actionPreview(int $productId = null): ?Response {
    //     if (!$productId) {
    //         return null;
    //     }

    //     $product = Product::find()->id($productId)->one();
    //     if (!$product) {
    //         throw new NotFoundHttpException('Product not found');
    //     }

    //     $previewPath = CraftShopify::$plugin->getSettings()->previewPath;

    //     return $this->renderTemplate($previewPath, [
    //         'product' => $product
    //     ], View::TEMPLATE_MODE_SITE);
    // }

    /**
     * Edit view for a product element
     *
     * @param int|null $productId
     * @param Product|null $product
     * @return Response
     * @throws NotFoundHttpException
     */
    // public function actionEditProduct(int $productId = null, Product $product = null): Response {
    //     $variables = [
    //         'productId' => $productId,
    //         'product' => $product
    //     ];

    //     $this->prepEditProductVariables($variables);

    //     $product = $variables['product'];

    //     /** @var Product $product */
    //     $variables['bodyClass'] = 'edit-product';
    //     if (CraftShopify::$plugin->getSettings()->previewPath) {
    //         $variables['previewUrl'] = UrlHelper::cpUrl('craft-shopify/products/' . $product->id . '/preview');
    //     }

    //     return $this->renderTemplate('craft-shopify/products/_edit', $variables);
    // }

    /**
     * Save Product element
     *
     * @return Response|null
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     * @throws BadRequestHttpException
     */
    // public function actionSave(): ?Response {
    //     $this->requirePostRequest();

    //     $productId = $this->request->getRequiredParam('productId');
    //     $product = Product::find()->id($productId)->one();
    //     // Check if the product exists
    //     if (!$product) {
    //         Craft::$app->getSession()->setNotice('Product not found');
    //         throw new Exception('Product not found');
    //     }

    //     $section = Craft::$app->getSections()->getSectionByHandle(
    //         'products'
    //     );

    //     if (!$section) {
    //         Craft::$app->getSession()->setNotice('Section not found');
    //         throw new Exception('Section not found');
    //     }
    //     Craft::$app->getSession()->setNotice('Section found');
    //     // $entry = new Entry();
    //     // $entry->sectionId = $section->id;
    //     // $entry->typeId = $section->getEntryTypes()[0]->id;
    //     // $entry->enabled = false;

    //     $entry = new Entry();
    //     $entry->sectionId = $section->id;
    //     $entry->typeId = $section->getEntryTypes()[0]->id;
    //     $entry->title = $product->title; // Set entry title to product title
    //     $entry->enabled = true; // You can set this to true if you want the entry to be enabled by default

    //     // Set other fields from the product as desired
    //     // For example:
    //     // $entry->setFieldValues([
    //     //     'fieldHandle1' => $product->fieldValue1,
    //     //     'fieldHandle2' => $product->fieldValue2,
    //     //     // Add other fields as needed
    //     // ]);

    //     // Save the entry
    //     if (!Craft::$app->getElements()->saveElement($entry)) {
    //         if ($this->request->getAcceptsJson()) {
    //             return $this->asJson([
    //                 'success' => false,
    //                 'errors' => $entry->getErrors()
    //             ]);
    //         }

    //         $this->setFailFlash('Couldn\'t save Product');
    //         return null;
    //     }

    //     if ($this->request->getAcceptsJson()) {
    //         return $this->asJson([
    //             'success' => true,
    //             'id' => $entry->id,
    //             'title' => $entry->title,
    //             // Add other fields as needed
    //         ]);
    //     }

    //     $this->setSuccessFlash('Product Saved');
    //     return $this->redirectToPostedUrl($entry);
    // }


    /**
     * Remove all products that are no longer in Shopify
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws Throwable
     * @throws ApiException
     * @throws CurlException
     * @since 1.1.0
     */
    // public function actionPurgeProducts(): ?Response {
    //     $this->requirePostRequest();

    //     $params = [
    //         'published_status' => 'published',
    //         'status' => 'active,draft',
    //         'limit' => -1
    //     ];

    //     $products = CraftShopify::$plugin->shopify->getAllProducts($params);
    //     $shopifyIds = ArrayHelper::getColumn($products, 'id');
    //     $errorCount = 0;
    //     $successCount = 0;

    //     $removed = Product::find()
    //         ->select(['elements.id', 'shopifyId'])
    //         ->where(['not in', 'shopifyId', $shopifyIds])
    //         ->all();

    //     $removedIds = ArrayHelper::getColumn($removed, 'shopifyId');
    //     foreach ($removedIds as $removedId) {
    //         if (!CraftShopify::$plugin->product->deleteByShopifyId($removedId)) {
    //             $errorCount++;
    //         } else {
    //             $successCount++;
    //         }
    //     }

    //     if ($errorCount > 0) {
    //         $this->setFailFlash('Failed to remove ' . $errorCount . ' products');
    //         return null;
    //     }

    //     $this->setSuccessFlash('Successfully removed ' . $successCount . ' products');
    //     return $this->redirectToPostedUrl();
    // }

    /**
     * Sync Craft product with Shopify
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws ApiException
     * @throws CurlException
     */
    public function actionSyncProducts(): Response {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $productIds = $request->getRequiredParam('productIds');

        $params = [
            'published_status' => 'published',
            'status' => 'active,draft',
            'limit' => -1
        ];

        if ($productIds !== '*') {
            $productIds = $this->normalizeArguments($productIds);
            $params['ids'] = implode(',', $productIds);
        }

        $products = CraftShopify::$plugin->shopify->getAllProducts($params);


        foreach ($products as $product) {
            $job = new SyncProduct();
            $job->productData = $product;

            Queue::push($job);
        }

        if ($this->request->getAcceptsJson()) {
            return $this->asJson([
                'success' => true
            ]);
        }

        $this->setSuccessFlash('Product Sync Started');
        return $this->redirectToPostedUrl();
    }

    /**
     * Normalizes values as an array of arguments.
     *
     * @param string|array|null $values
     *
     * @return string[]
     */
    private function normalizeArguments($values): array {

        if (is_string($values)) {
            $values = StringHelper::split($values);
        }

        if (is_array($values)) {
            // Flatten multi-dimensional arrays
            array_walk($values, function (&$value) {
                if (is_array($value)) {
                    $value = reset($value);
                }
            });

            // Remove empty values
            return array_filter($values);
        }

        return [];
    }
}
