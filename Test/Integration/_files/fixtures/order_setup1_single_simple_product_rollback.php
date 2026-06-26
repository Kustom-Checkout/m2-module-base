<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

use Magento\Framework\Registry;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();

/** @var QuoteFactory $quoteFactory */
$quoteFactory = $objectManager->get(QuoteFactory::class);
/** @var OrderFactory $orderFactory */
$orderFactory = $objectManager->get(OrderFactory::class);
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$order = $orderFactory->create()->loadByIncrementId('100000001');
if ($order->getId()) {
    $order->delete();
}

$quote = $quoteFactory->create()->load('100000001', 'reserved_order_id');
if ($quote->getId()) {
    $quote->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

Resolver::getInstance()->requireDataFixture('Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104_rollback.php');
Resolver::getInstance()->requireDataFixture('Klarna_Base::Test/Integration/_files/fixtures/product_simple_rollback.php');
