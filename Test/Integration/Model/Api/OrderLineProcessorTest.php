<?php
/**
 * Copyright © Kustom AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Model\Api;

use Klarna\Base\Model\Api\OrderLineProcessor;
use Klarna\Orderlines\Model\Container\Parameter;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Item;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class OrderLineProcessorTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var OrderLineProcessor
     */
    private $processor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = Bootstrap::getObjectManager();
        $this->processor = $this->objectManager->create(OrderLineProcessor::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testProcessByInvoiceSetsCurrentValuesOnOrderItem(): void
    {
        $orderItem = $this->createOrderItem();
        $invoice = $this->createDocument(Invoice::class, $orderItem);

        $this->processor->processByInvoice($this->createParameter(), $invoice);

        $this->assertCurrentValues($orderItem);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testProcessByCreditMemoSetsCurrentValuesOnOrderItem(): void
    {
        $orderItem = $this->createOrderItem();
        $creditMemo = $this->createDocument(Creditmemo::class, $orderItem);

        $this->processor->processByCreditMemo($this->createParameter(), $creditMemo);

        $this->assertCurrentValues($orderItem);
    }

    private function assertCurrentValues(Item $orderItem): void
    {
        $this->assertEquals(1, $orderItem->getCurrentInvoiceRefundItemQty());
        $this->assertEquals(39.20, $orderItem->getCurrentInvoiceRefundItemBaseRowTotal());
        $this->assertEquals(49.00, $orderItem->getCurrentInvoiceRefundItemBaseRowTotalInclTax());
        $this->assertEquals(8.82, $orderItem->getCurrentInvoiceRefundItemBaseTaxAmount());
        $this->assertEquals(4.90, $orderItem->getCurrentInvoiceRefundItemBaseDiscountAmount());
        $this->assertEquals(0.98, $orderItem->getCurrentInvoiceRefundItemBaseDiscountTaxCompensationAmount());
    }

    private function createParameter(): Parameter
    {
        return $this->objectManager->create(Parameter::class, ['entities' => []]);
    }

    private function createOrderItem(): Item
    {
        $orderItem = $this->objectManager->create(Item::class);
        $orderItem->setQtyOrdered(2);

        return $orderItem;
    }

    /**
     * @param string $class Invoice or Creditmemo class
     * @param Item $orderItem
     * @return Invoice|Creditmemo
     */
    private function createDocument(string $class, Item $orderItem)
    {
        $store = $this->objectManager->get(StoreManagerInterface::class)->getStore();

        $order = $this->objectManager->create(Order::class);
        $order->setStore($store);
        $order->setIsVirtual(0);

        $document = $this->objectManager->create($class);
        $document->setOrder($order);
        $document->setStoreId((int) $store->getId());

        $documentItem = $this->objectManager->create($class . '\Item');
        $documentItem->setOrderItem($orderItem);
        $documentItem->setQty(1);
        $documentItem->setBaseRowTotal(39.20);
        $documentItem->setBaseRowTotalInclTax(49.00);
        $documentItem->setBaseTaxAmount(8.82);
        $documentItem->setBaseDiscountAmount(4.90);
        $documentItem->setBaseDiscountTaxCompensationAmount(0.98);
        $document->setItems([$documentItem]);

        return $document;
    }
}
