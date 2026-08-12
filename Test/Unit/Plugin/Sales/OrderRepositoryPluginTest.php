<?php
/**
 * Copyright © Kustom AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Plugin\Sales;

use Klarna\Base\Api\OrderRepositoryInterface as KlarnaOrderRepositoryInterface;
use Klarna\Base\Model\Order as KlarnaOrder;
use Klarna\Base\Plugin\Sales\OrderRepositoryPlugin;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use PHPUnit\Framework\TestCase;


/**
 * @coversDefaultClass \Klarna\Base\Plugin\Sales\OrderRepositoryPlugin
 */
class OrderRepositoryPluginTest extends TestCase
{
    /**
     * @var OrderRepositoryPlugin
     */
    private $plugin;
    /**
     * @var KlarnaOrderRepositoryInterface
     */
    private $klarnaOrderRepository;
    /**
     * @var ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @covers ::afterGet()
     */
    public function testAfterGetPopulatesExtensionAttributes(): void
    {
        $klarnaOrder = $this->createMock(KlarnaOrder::class);
        $klarnaOrder->method('getTosId')->willReturn('tos-id-123');
        $klarnaOrder->method('getShippingCarrier')->willReturn('ingrid');
        $klarnaOrder->method('getShippingLocationName')->willReturn('7-Eleven Main St');
        $klarnaOrder->method('getSelectedShippingOption')->willReturn('{"id":"shipping-1"}');

        $this->klarnaOrderRepository->method('getByOrder')->willReturn($klarnaOrder);

        $extensionAttributes = $this->createMock(KustomOrderExtensionTestInterface::class);

        $extensionAttributes->expects(static::once())->method('setKustomTosId')->with('tos-id-123');
        $extensionAttributes->expects(static::once())->method('setKustomShippingCarrier')->with('ingrid');
        $extensionAttributes->expects(static::once())
            ->method('setKustomShippingLocationName')
            ->with('7-Eleven Main St');
        $extensionAttributes->expects(static::once())
            ->method('setKustomSelectedShippingOption')
            ->with('{"id":"shipping-1"}');

        $order = $this->createMock(OrderInterface::class);
        $order->method('getExtensionAttributes')->willReturn($extensionAttributes);
        $order->expects(static::once())->method('setExtensionAttributes')->with($extensionAttributes);

        $subject = $this->createMock(OrderRepositoryInterface::class);

        $result = $this->plugin->afterGet($subject, $order);

        static::assertSame($order, $result);
    }

    /**
     * @covers ::afterGet()
     */
    public function testAfterGetDoesNothingWhenNoKlarnaOrderExists(): void
    {
        $this->klarnaOrderRepository
            ->method('getByOrder')
            ->willThrowException(new NoSuchEntityException(__('not found')));

        $order = $this->createMock(OrderInterface::class);
        $order->expects(static::never())->method('setExtensionAttributes');

        $subject = $this->createMock(OrderRepositoryInterface::class);

        $result = $this->plugin->afterGet($subject, $order);

        static::assertSame($order, $result);
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->klarnaOrderRepository = $this->createMock(KlarnaOrderRepositoryInterface::class);
        $this->extensionAttributesFactory = $this->getMockBuilder(ExtensionAttributesFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new OrderRepositoryPlugin(
            $this->klarnaOrderRepository,
            $this->extensionAttributesFactory
        );
    }
}

