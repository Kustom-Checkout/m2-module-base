<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture(
    'Klarna_Base::Test/Integration/_files/fixtures/order_setup1_single_simple_product.php'
);

$objectManager = Bootstrap::getObjectManager();

/** @var \Klarna\Base\Model\OrderFactory $kOrderFactory */
$kOrderFactory = $objectManager->get(\Klarna\Base\Model\OrderFactory::class);
/** @var \Magento\Sales\Model\OrderFactory $mOrderFactory */
$mOrderFactory = $objectManager->get(\Magento\Sales\Model\OrderFactory::class);

$order = $mOrderFactory->create()->loadByIncrementId('100000001');
$klarnaOrder = $kOrderFactory->create();
$klarnaOrder->setKlarnaOrderId('123456-1234-1234-1234-1234567890');
$klarnaOrder->setOrderId($order->getId());
$klarnaOrder->save();
