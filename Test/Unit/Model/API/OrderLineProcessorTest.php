<?php
/**
 * Copyright © Kustom AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Model\Api;

use Klarna\Base\Model\Api\OrderLineProcessor;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * @coversDefaultClass \Klarna\Base\Model\Api\OrderLineProcessor
 */
class OrderLineProcessorTest extends TestCase
{
    /**
     * @var OrderLineProcessor
     */
    private OrderLineProcessor $model;

    public function testGetItemsFromPurchaseObjectCopiesTheAmountsOfTheInvoiceItemToTheOrderItem(): void
    {
        $orderItem = $this->mockFactory->create(OrderItem::class, ['getProduct']);
        $orderItem->setData([
            'qty_ordered' => 2,
            'base_row_total' => 78.40,
            'base_tax_amount' => 19.60,
            'base_discount_amount' => 9.80,
            'base_discount_tax_compensation_amount' => 1.96,
        ]);

        $invoiceItem = $this->mockFactory->create(InvoiceItem::class, ['getOrderItem']);
        $invoiceItem->method('getOrderItem')
            ->willReturn($orderItem);
        $invoiceItem->setData([
            'qty' => 1,
            'base_row_total' => 39.20,
            'base_row_total_incl_tax' => 49.00,
            'base_tax_amount' => 8.82,
            'base_discount_amount' => 4.90,
            'base_discount_tax_compensation_amount' => 0.98,
        ]);

        $method = new \ReflectionMethod(OrderLineProcessor::class, 'getItemsFromPurchaseObject');
        $result = $method->invoke($this->model, [$invoiceItem]);

        static::assertSame([$orderItem], $result);
        static::assertEquals(1, $orderItem->getCurrentInvoiceRefundItemQty());
        static::assertEquals(39.20, $orderItem->getCurrentInvoiceRefundItemBaseRowTotal());
        static::assertEquals(8.82, $orderItem->getCurrentInvoiceRefundItemBaseTaxAmount());
        static::assertEquals(4.90, $orderItem->getCurrentInvoiceRefundItemBaseDiscountAmount());
        static::assertEquals(0.98, $orderItem->getCurrentInvoiceRefundItemBaseDiscountTaxCompensationAmount());
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(OrderLineProcessor::class);
    }
}
