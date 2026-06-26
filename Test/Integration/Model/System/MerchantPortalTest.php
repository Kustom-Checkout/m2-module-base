<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Model\System;

use Klarna\Base\Api\OrderInterface as KlarnaOrder;
use Klarna\Base\Model\System\MerchantPortal;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\Data\OrderInterface as MageOrder;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Klarna\Base\Model\System\MerchantPortal
 */
class MerchantPortalTest extends TestCase
{
    /**
     * @var ObjectManagerInterface|null
     */
    private ?ObjectManagerInterface $objectManager = null;

    /**
     * @var MerchantPortal
     */
    private ?MerchantPortal $model = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(MerchantPortal::class);
    }

    /**
     * @magentoConfigFixture current_store klarna/api_eu/api_mode 0
     * @magentoConfigFixture current_store klarna/api_eu/client_identifier_production MERCHANT-123
     * @covers ::getOrderMerchantPortalLink
     */
    public function testGetOrderMerchantPortalLinkToLive(): void
    {
        /** @var MageOrder $mageOrder */
        $mageOrder = $this->objectManager->create(MageOrder::class);
        $mageOrder->setOrderCurrencyCode('currency_code');

        /** @var KlarnaOrder $klarnaOrder */
        $klarnaOrder = $this->objectManager->create(KlarnaOrder::class);
        $klarnaOrder->setKlarnaOrderId('ORDER-123');

        $urlPath = 'orders/list/ORDER-123?merchantId=MERCHANT-123';
        $expected = MerchantPortal::MERCHANT_PORTAL . $urlPath;

        $result = $this->model->getOrderMerchantPortalLink($mageOrder, $klarnaOrder);
        $this->assertEquals($result, $expected);
    }

    /**
     * @magentoConfigFixture current_store klarna/api_eu/api_mode 1
     * @magentoConfigFixture current_store klarna/api_eu/client_identifier_playground MERCHANT-123
     * @covers ::getOrderMerchantPortalLink
     */
    public function testGetOrderMerchantPortalLinkToTest(): void
    {
        /** @var MageOrder $mageOrder */
        $mageOrder = $this->objectManager->create(MageOrder::class);
        $mageOrder->setOrderCurrencyCode('currency_code');

        /** @var KlarnaOrder $klarnaOrder */
        $klarnaOrder = $this->objectManager->create(KlarnaOrder::class);
        $klarnaOrder->setKlarnaOrderId('ORDER-123');

        $urlPath = 'orders/list/ORDER-123?merchantId=MERCHANT-123';
        $expected = MerchantPortal::MERCHANT_TEST_PORTAL . $urlPath;

        $result = $this->model->getOrderMerchantPortalLink($mageOrder, $klarnaOrder);
        $this->assertEquals($result, $expected);
    }
}
