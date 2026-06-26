<?php
/**
 * Copyright © Klarna Bank AB (publ)
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
use Magento\Framework\App\State;
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
        $this->setUpKlarna('adminhtml');

        $expected = 'https://x.klarnacdn.net/payment-method/assets/badges/generic/white/klarna.png?width=300';
        $result = $this->klarna->getLogoUrl();

        static::assertEquals($expected, $result);
    }

    public function testGetFullSpecificInformationWrongAreaNotContainsLogLink(): void
    {
        $this->setUpKlarna('frontend');

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertFalse(isset($result['Logs']));
    }

    public function testGetFullSpecificInformationContainsSessionLogLink(): void
    {
        $this->setUpKlarna('adminhtml');

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertSame('klarna/index/logs::session_id', $result['Logs']);
    }

    public function testGetFullSpecificInformationContainsKlarnaOrderLogLink(): void
    {
        $this->setUpKlarna('adminhtml');

        $this->setUpLogLinkTest(null, 'klarna_order_id');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertSame('klarna/index/logs::klarna_order_id', $result['Logs']);
    }

    public function testAdminGetFullSpecificInformationContainsAuthorizedPaymentMethod(): void
    {
        $this->setUpKlarna('adminhtml');

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertSame('DIRECT_DEBIT', $result['Authorized Payment Method']);
    }

    public function testFrontFullSpecificInformationDoesNotContainsAuthorizedPaymentMethod(): void
    {
        $this->setUpKlarna('frontend');

        $this->setUpLogLinkTest('session_id', 'klarna_order_id');
        $result = $this->klarna->getFullSpecificInformation();

        static::assertFalse(isset($result['Authorized Payment Method']));
    }

    private function setUpLogLinkTest($sessionId, $klarnaOrderId): void
    {
        $klarnaOrder = $this->mockFactory->create(KlarnaOrder::class);
        $klarnaOrder
            ->method('getId')
            ->willReturn(1);
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

        $klarnaOrder
            ->method('getSessionId')
            ->willReturn($sessionId);
        $klarnaOrder
            ->method('getKlarnaOrderId')
            ->willReturn($klarnaOrderId);
        $klarnaOrder
            ->method('getAuthorizedPaymentMethod')
            ->willReturn('direct_debit');
    }

    /**
     * Context/app state/area code need to be set before instance creation, otherwise
     * $context->getAppState() in the constructor returns null
     *
     * @param string $areaCode
     */
    private function setUpKlarna(string $areaCode): void
    {
        $context = $this->mockFactory->create(Context::class);
        $appState = $this->mockFactory->create(State::class);
        $appState
            ->method('getAreaCode')
            ->willReturn($areaCode);
        $context
            ->method('getAppState')
            ->willReturn($appState);

        $objectFactory = new TestObjectFactory('');
        $this->klarna = $objectFactory->create(
            Klarna::class,
            [],
            [
                Context::class => $context
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
