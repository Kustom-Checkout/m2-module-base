<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Helper;

use PHPUnit\Framework\Assert;

/**
 * @internal
 */
class PostPurchaseValidator extends ValidatorAbstract
{
    public function performAllGeneralUsChecks(array $request, int $amount, $order)
    {
        $orderLines = $request['order_lines'];

        $this->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $amount);
        $this->isKlarnaShopUsTaxSame($orderLines, $order);
        $this->isKlarnaShopShippingPriceSame($orderLines, $order);
        $this->isKlarnaUsSumProductTotalsShopSubtotalSame($orderLines, $order);
        $this->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->isKlarnaUsTaxShippingTotalEqualUnitQty($orderLines);
        $this->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->isKlarnaSumTotalsKlarnaOrderAmountSame($request, $amount);
        $this->isKlarnaOrderAmountShopOrderAmountSame($amount, $order);
    }

    public function isKlarnaOrderAmountShopOrderAmountSame(int $amount, $entity)
    {
        $shopGrandTotal = round($entity->getGrandTotal() * 100);
        Assert::assertEquals($amount, $shopGrandTotal);
    }

    public function isKlarnaSumTotalsKlarnaOrderAmountSame(array $request, int $amount)
    {
        $klarnaSumTotals = $this->getFullTotalAmount($request['order_lines']);
        Assert::assertEquals($amount, $klarnaSumTotals);
    }

    public function isKlarnaUsSumProductTotalsShopSubtotalSame(array $orderlines, $order)
    {
        $klarnaProductTotals = 0;
        $productItems = $this->getAllProductOrderlineItems($orderlines);
        foreach ($productItems as $item) {
            $klarnaProductTotals += $item['total_amount'];
        }

        $klarnaProductTotals = round($klarnaProductTotals);
        $shopSubTotal = round($order->getSubTotal() * 100);

        Assert::assertSame($shopSubTotal, $klarnaProductTotals, "Product total check failed: Klarna product sum totals = $klarnaProductTotals is unequal to shop subtotal = $shopSubTotal"); // phpcs:ignore
    }

    public function isKlarnaShopShippingPriceSame(array $orderlines, $order)
    {
        try {
            $shippingOrderlineItem = $this->getShippingOrderlineItem($orderlines);
        } catch (\Exception $e) {
            return;
        }

        $klarnaShippingCosts = round($shippingOrderlineItem['total_amount'] + $shippingOrderlineItem['total_discount_amount']); // phpcs:ignore
        $shopShippingCosts = round($order->getBaseShippingAmount() * 100);

        Assert::assertSame($shopShippingCosts, $klarnaShippingCosts, "Klarna shop shipping check failed: Shop shipping costs = $shopShippingCosts is unequal to total_amount = $klarnaShippingCosts"); // phpcs:ignore
    }

    public function isKlarnaShopUsTaxSame(array $orderlines, $order)
    {
        $klarnaTaxItem = $this->getUsTaxOrderlineItem($orderlines);
        $klarnaTax = round($klarnaTaxItem['total_amount']);
        $shopTax = round($order->getBaseTaxAmount() * 100);

        Assert::assertSame($shopTax, $klarnaTax, "US tax check failed: Shop tax = $shopTax is unequal to total_amount = $klarnaTax"); // phpcs:ignore
    }

    public function isKlarnaSumTotalsShopGrandTotalSame(array $orderlines, int $klarnaTotalAmount)
    {
        $klarnaTotalSum = $this->getFullTotalAmount($orderlines);
        Assert::assertEquals($klarnaTotalSum, $klarnaTotalAmount); // phpcs:ignore
    }
}
