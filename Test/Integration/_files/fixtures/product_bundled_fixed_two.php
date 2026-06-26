<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Bundle\Model\Product\Price;

/*
 * Since the bundle product creation GUI doesn't allow to choose values for bundled products' custom options,
 * bundled items should not contain products with required custom options.
 * However, if to create such a bundle product, it will be always out of stock.
 */
Resolver::getInstance()->requireDataFixture('Klarna_Base::Test/Integration/_files/fixtures/product_simple_three.php');

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
$sampleProduct = $productRepository->get('simple-2');

/** @var $product \Magento\Catalog\Model\Product */
$product = $objectManager->create(\Magento\Catalog\Model\Product::class);
$product->setTypeId('bundle')
    ->setAttributeSetId(4)
    ->setWeight(2)
    ->setWebsiteIds([1])
    ->setName('Bundle Product 2')
    ->setSku('bundle-product-two')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setPriceView(1)
    ->setSkuType(1)
    ->setWeightType(1)
    ->setPriceType(Price::PRICE_TYPE_FIXED)
    ->setShipmentType(0)
    ->setPrice(11.0)
    ->setBundleOptionsData(
        [
            [
                'title' => 'Bundle Product Items Two',
                'default_title' => 'Bundle Product Items Two',
                'type' => 'select', 'required' => 1,
                'delete' => '',
            ],
        ]
    )
    ->setBundleSelectionsData(
        [
            [
                [
                    'product_id' => $sampleProduct->getId(),
                    'selection_price_value' => 4.75,
                    'selection_qty' => 1,
                    'selection_can_change_qty' => 1,
                    'delete' => '',

                ],
            ],
        ]
    );

$productRepository->save($product);
if ($product->getBundleOptionsData()) {
    $options = [];
    foreach ($product->getBundleOptionsData() as $key => $optionData) {
        if (!(bool)$optionData['delete']) {
            $option = $objectManager->create(\Magento\Bundle\Api\Data\OptionInterfaceFactory::class)
                ->create(['data' => $optionData]);
            $option->setSku($product->getSku());
            $option->setOptionId(null);

            $links = [];
            $bundleLinks = $product->getBundleSelectionsData();
            if (!empty($bundleLinks[$key])) {
                foreach ($bundleLinks[$key] as $linkData) {
                    if (!(bool)$linkData['delete']) {
                        /** @var \Magento\Bundle\Api\Data\LinkInterface$link */
                        $link = $objectManager->create(\Magento\Bundle\Api\Data\LinkInterfaceFactory::class)
                            ->create(['data' => $linkData]);
                        $linkProduct = $productRepository->getById($linkData['product_id']);
                        $link->setSku($linkProduct->getSku());
                        $link->setQty($linkData['selection_qty']);
                        $link->setPrice($linkData['selection_price_value']);
                        if (isset($linkData['selection_can_change_qty'])) {
                            $link->setCanChangeQuantity($linkData['selection_can_change_qty']);
                        }
                        $links[] = $link;
                    }
                }
                $option->setProductLinks($links);
                $options[] = $option;
            }
        }
    }
    $extension = $product->getExtensionAttributes();
    $extension->setBundleProductOptions($options);
    $product->setExtensionAttributes($extension);
}

$product = $productRepository->save($product, true);

$productHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Catalog\Helper\Product::class);
$productHelper->setSkipSaleableCheck(true);

$stockCheckSql = "SELECT * FROM cataloginventory_stock_status WHERE product_id = " . $product->getId();
$connection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
$stockCheckResult = $connection->fetchAll($stockCheckSql);
if (count($stockCheckResult) === 0) {
    $stockSql = "INSERT INTO cataloginventory_stock_status (product_id, website_id, stock_id, qty, stock_status) " .
        "VALUES (" . $product->getId() . ",0,1,12345,1)";
    $connection->query($stockSql);
}
