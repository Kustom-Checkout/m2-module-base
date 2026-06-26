<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

use Klarna\Base\Model\OrderFactory;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();

/** @var OrderFactory $kOrderFactory */
$kOrderFactory = $objectManager->get(OrderFactory::class);
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$klarnaOrder = $kOrderFactory->create()->load('123456-1234-1234-1234-1234567890', 'klarna_order_id');
if ($klarnaOrder->getId()) {
    $klarnaOrder->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

Resolver::getInstance()->requireDataFixture(
    'Klarna_Base::Test/Integration/_files/fixtures/order_setup1_single_simple_product_rollback.php'
);
