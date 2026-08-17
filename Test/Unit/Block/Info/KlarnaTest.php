<?php
/**
 * Copyright © Kustom AB (Originally developed by Klarna Bank AB)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Block\Info;

use Klarna\Base\Block\Info\Klarna;
use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\TestCase;
use Magento\Framework\DataObjectFactory;
use Klarna\Base\Model\Order as KlarnaOrder;
use Magento\Sales\Model\Order as MageOrder;
use Magento\Framework\View\Element\Template\Context;

/**
 * @coversDefaultClass \Klarna\Base\Block\Info\Klarna
 */
class KlarnaTest extends TestCase
{
    /**
     * @var Klarna
     */
    private Klarna $klarna;
    /**
     * @var MockObject[]
     */
    private $dependencyMocks;
    /**
     * @var MockFactory
     */
    private $mockFactory;

    public function testGetLogoUrlReturnsCorrectUrl(): void
    {
        $this->setUpKlarna();

        $expected = 'https://cdn.kustom.co/assets/badges/kustom_logo_black.png';
        $result = $this->klarna->getLogoUrl();

        static::assertEquals($expected, $result);
    }

    /**
     * getSpecificInformation() is the non-admin (e.g. invoice PDF) path — it sets
     * isAdminArea = false internally, so admin-only fields must be absent.
     */
    public function testGetSpecificInformationDoesNotContainLogLink(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getSpecificInformation();

        static::assertFalse(isset($result['Logs']));
    }

    public function testGetSpecificInformationDoesNotContainAuthorizedPaymentMethod(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getSpecificInformation();

        static::assertFalse(isset($result['Authorized Payment Method']));
    }

    public function testGetSpecificInformationDoesNotContainShippingOptionDetails(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getSpecificInformation();

        static::assertFalse(isset($result['TOS ID']));
        static::assertFalse(isset($result['Shipping Carrier']));
        static::assertFalse(isset($result['Pickup Location']));
    }

    public function testGetSpecificInformationDoesNotContainMerchantPortalLink(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $this->dependencyMocks['merchantPortal']
            ->method('getOrderMerchantPortalLink')
            ->willReturn('https://portal.klarna.com/some-link');
        $result = $this->klarna->getSpecificInformation();

        static::assertFalse(isset($result['Merchant Portal']));
    }

    /**
     * getFullSpecificInformation() is the admin path — isAdminArea stays at its
     * default (true), so admin-only fields must be present.
     */
    public function testGetFullSpecificInformationContainsSessionLogLink(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertSame('klarna/index/logs::session_id', $result['Logs']);
    }

    public function testGetFullSpecificInformationContainsKlarnaOrderLogLink(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest(null, 'klarna_order_id');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertSame('klarna/index/logs::klarna_order_id', $result['Logs']);
    }

    public function testGetFullSpecificInformationContainsAuthorizedPaymentMethod(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertSame('DIRECT_DEBIT', $result['Authorized Payment Method']);
    }

    public function testGetFullSpecificInformationDoesNotContainAuthorizedPaymentMethodWhenAbsent(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id', '');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertFalse(isset($result['Authorized Payment Method']));
    }

    public function testGetFullSpecificInformationContainsShippingOptionDetails(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertSame('tos-id-123', $result['TOS ID']);
        static::assertSame('ingrid', $result['Shipping Carrier']);
        static::assertFalse(isset($result['Pickup Location']));
    }

    public function testGetFullSpecificInformationContainsMerchantPortalLinkWhenAvailable(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $this->dependencyMocks['merchantPortal']
            ->method('getOrderMerchantPortalLink')
            ->willReturn('https://portal.klarna.com/some-link');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertSame('https://portal.klarna.com/some-link', $result['Merchant Portal']);
    }

    public function testGetFullSpecificInformationDoesNotContainMerchantPortalLinkWhenUnavailable(): void
    {
        $this->setUpKlarna();

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $this->dependencyMocks['merchantPortal']
            ->method('getOrderMerchantPortalLink')
            ->willReturn('');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertFalse(isset($result['Merchant Portal']));
    }

    private function setUpLogLinkTest($sessionId, $klarnaOrderId, $authorizedPaymentMethod = 'direct_debit'): void
    {
        $klarnaOrder = $this->buildKlarnaOrderMock($sessionId, $klarnaOrderId, $authorizedPaymentMethod);

        $mageOrder = $this->mockFactory->create(MageOrder::class);
        $mageOrder
            ->method('getInvoiceCollection')
            ->willReturn([]);

        $info = $this->mockFactory->create(Payment::class);
        $info
            ->method('getOrder')
            ->willReturn($mageOrder);
        $this->klarna->addData([
            'info' => $info
        ]);
        $this->dependencyMocks['dataObjectFactory']
            ->method('create')
            ->willReturn(new DataObject([]));
        $this->dependencyMocks['orderRepository']
            ->method('getByOrder')
            ->willReturn($klarnaOrder);

        $this->dependencyMocks['urlBuilder']
            ->method('getUrl')
            ->willReturnCallback(function ($routePath, $routeParams) {
                return sprintf('%s::%s', $routePath, $routeParams['klarna_id']);
            });
    }

    /**
     * Builds a Klarna order mock with sensible defaults for id/reservation/shipping,
     * letting individual tests vary the session id, order id, and payment method.
     */
    private function buildKlarnaOrderMock($sessionId, $klarnaOrderId, $authorizedPaymentMethod)
    {
        $klarnaOrder = $this->mockFactory->create(KlarnaOrder::class);
        $klarnaOrder
            ->method('getId')
            ->willReturn(1);
        $klarnaOrder
            ->method('getSessionId')
            ->willReturn($sessionId);
        $klarnaOrder
            ->method('getKlarnaOrderId')
            ->willReturn($klarnaOrderId);
        $klarnaOrder
            ->method('getAuthorizedPaymentMethod')
            ->willReturn($authorizedPaymentMethod);
        $klarnaOrder
            ->method('getTosId')
            ->willReturn('tos-id-123');
        $klarnaOrder
            ->method('getShippingCarrier')
            ->willReturn('ingrid');
        $klarnaOrder
            ->method('getShippingLocationName')
            ->willReturn(null);

        return $klarnaOrder;
    }

    private function setUpKlarna(): void
    {
        $objectFactory = new TestObjectFactory('');
        $this->klarna = $objectFactory->create(
            Klarna::class,
            [],
            [
                Context::class => $this->mockFactory->create(Context::class)
            ]
        );
        $this->dependencyMocks = $objectFactory->getDependencyMocks();
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->mockFactory = new MockFactory($this);
    }
}
