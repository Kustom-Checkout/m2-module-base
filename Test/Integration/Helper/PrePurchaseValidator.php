<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Helper;

use Magento\Quote\Api\Data\CartInterface;
use PHPUnit\Framework\Assert;

/**
 * @internal
 */
class PrePurchaseValidator extends ValidatorAbstract
{
    public function isKlarnaShippingTotalEqualUnitQty(array $orderlines)
    {
        try {
            $shippingItem = $this->getShippingOrderlineItem($orderlines);
        } catch (\Exception $e) {
            return;
        }
        $total = $shippingItem['total_amount'] + $shippingItem['total_discount_amount'];
        $calculatedTotal = $shippingItem['quantity'] * $shippingItem['unit_price'];

        Assert::assertSame(round($total), round($calculatedTotal), "Shipping check failed: total_amount + total_discount_amount = $total but its unequal to qty * unit_price = $calculatedTotal"); // phpcs:ignore
    }

    public function isKlarnaProductTotalEqualUnitQty(array $orderlines)
    {
        $productItems = $this->getAllProductOrderlineItems($orderlines);
        foreach ($productItems as $item) {
            $total = round($item['total_amount'] + $item['total_discount_amount']);
            $calculatedTotal = round($item['quantity'] * $item['unit_price']);

            $diff = abs($total - $calculatedTotal);
            $boolResult = in_array($diff, [0, 1]);

            Assert::assertTrue($boolResult);
        }
    }

    public function isKlarnaUsTaxShippingTotalEqualUnitQty(array $orderlines)
    {
        $taxItem = $this->getUsTaxOrderlineItem($orderlines);
        $total = round($taxItem['total_amount']);
        $calculatedTotal = round($taxItem['quantity'] * $taxItem['unit_price']);

        Assert::assertSame($total, $calculatedTotal, "US shipping check failed: total_amount = $total but its unequal to qty * unit_pice = $calculatedTotal"); // phpcs:ignore
    }

    public function isKlarnaShopShippingPriceSame(array $orderlines, CartInterface $quote)
    {
        try {
            $shippingOrderlineItem = $this->getShippingOrderlineItem($orderlines);
            $klarnaShippingCosts = round($shippingOrderlineItem['total_amount'] + $shippingOrderlineItem['total_discount_amount']); // phpcs
        } catch (\Exception $e) {
            $klarnaShippingCosts = 0;
        }
        $shopShippingCosts = round($quote->getShippingAddress()->getShippingAmount() * 100);

        Assert::assertSame((float) $shopShippingCosts, (float) $klarnaShippingCosts, "Klarna shop shipping check failed: Shop shipping costs = $shopShippingCosts is unequal to total_amount = $klarnaShippingCosts"); // phpcs:ignore
    }

    public function isKlarnaShopShippingMethodSame(array $orderLines, CartInterface $quote): void
    {
        try {
            $shippingOrderlineItem = $this->getShippingOrderlineItem($orderLines);
        } catch (\Exception $e) {
            return;
        }

        $klarnaShippingType = $shippingOrderlineItem['reference'];
        $shopShippingType = $quote->getShippingAddress()->getShippingMethod();

        Assert::assertSame($klarnaShippingType, $shopShippingType, "Klarna shop shipping check failed: Shop shipping name = $shopShippingType is unequal to order line shipping name = $klarnaShippingType"); // phpcs:ignore
    }

    public function isKlarnaShopUsTaxSame(array $orderlines, CartInterface $quote)
    {
        $klarnaTaxItem = $this->getUsTaxOrderlineItem($orderlines);
        $klarnaTax = round($klarnaTaxItem['total_amount']);
        $shopTax = round($quote->getTotals()['tax']->getValue() * 100);

        Assert::assertSame($shopTax, $klarnaTax, "US tax check failed: Shop tax = $shopTax is unequal to total_amount = $klarnaTax"); // phpcs:ignore
    }

    public function isKlarnaShopTaxSame(array $orderlines, CartInterface $quote)
    {
        $klarnaTax = $this->getFullTotalTaxAmount($orderlines);
        $shopTax = round($quote->getTotals()['tax']->getValue() * 100);
        Assert::assertSame($shopTax, $klarnaTax, "Tax check failed: Shop tax = $shopTax is unqual to Klarna tax = $klarnaTax"); // phpcs:ignore
    }

    public function isKlarnaSumTotalsShopGrandTotalSame(array $orderlines, CartInterface $quote)
    {
        $klarnaGrandTotal = $this->getFullTotalAmount($orderlines);
        $shopGrandTotal = round($quote->getBaseGrandTotal() * 100);

        $diff = abs($klarnaGrandTotal - $shopGrandTotal);
        $boolResult = in_array($diff, [0, 1]);
        Assert::assertTrue($boolResult, "Grand total check failed: Klarna sum totals = $klarnaGrandTotal is unequal to shop grand_total = $shopGrandTotal"); // phpcs:ignore
    }

    public function isKlarnaUsSumProductTotalsShopSubtotalSame(array $orderlines, CartInterface $quote)
    {
        $klarnaProductTotals = 0;
        foreach ($orderlines as $item) {
            if ($item['type'] === 'sales_tax') {
                continue;
            }
            if ($item['type'] === 'shipping_fee') {
                $klarnaProductTotals -= $item['total_discount_amount'];
                continue;
            }
            $klarnaProductTotals += $item['total_amount'];
        }

        $klarnaProductTotals = round($klarnaProductTotals);
        $shopSubTotal = round($quote->getBaseSubtotalWithDiscount() * 100);

        Assert::assertNotEquals((float) 0, $klarnaProductTotals);
        Assert::assertSame($shopSubTotal, $klarnaProductTotals, "Product total check failed: Klarna product sum totals = $klarnaProductTotals is unequal to shop subtotal = $shopSubTotal"); // phpcs:ignore
    }

    public function performAllGeneralUsChecks(array $request, CartInterface $quote, array $flags = [])
    {
        $orderLines = $request['order_lines'];

        $this->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->isKlarnaShopUsTaxSame($orderLines, $quote);
        $this->isKlarnaShopShippingPriceSame($orderLines, $quote);
        $this->isKlarnaShopShippingMethodSame($orderLines, $quote);

        if (!in_array('skip_product_total_check', $flags)) {
            $this->isKlarnaUsSumProductTotalsShopSubtotalSame($orderLines, $quote);
        }
        $this->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->isKlarnaUsTaxShippingTotalEqualUnitQty($orderLines);
        $this->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    public function isKlarnaOrderTaxAmountZero($request)
    {
        $klarnaOrderTaxAmount = $request['order_tax_amount'];
        Assert::assertEquals(0, $klarnaOrderTaxAmount);
    }

    public function isKlarnaOrderTaxAmountNotZero($request)
    {
        $klarnaOrderTaxAmount = $request['order_tax_amount'];
        Assert::assertNotEquals(0, $klarnaOrderTaxAmount);
    }

    public function performAllGeneralChecks(array $request, CartInterface $quote)
    {
        $orderLines = $request['order_lines'];

        $this->isKlarnaSumTotalsShopGrandTotalSame($orderLines, $quote);
        $this->isKlarnaShopTaxSame($orderLines, $quote);
        $this->isKlarnaShippingTotalEqualUnitQty($orderLines);
        $this->isKlarnaProductTotalEqualUnitQty($orderLines);
        $this->isKlarnaSumTotalsKlarnaOrderAmountSame($request);
        $this->isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame($request);
        $this->isKlarnaOrderAmountShopOrderAmountSame($request, $quote);
        $this->isKlarnaOrderTaxAmountShopTaxSame($request, $quote);
    }

    public function isKlarnaSumTotalsKlarnaOrderAmountSame(array $request)
    {
        $klarnaSumTotals = $this->getFullTotalAmount($request['order_lines']);
        Assert::assertEquals($request['order_amount'], $klarnaSumTotals);
    }

    public function isKlarnaSumTaxTotalsKlarnaOrderTaxAmountSame(array $request)
    {
        $klarnaSumTaxTotals = $this->getFullTotalTaxAmount($request['order_lines']);
        Assert::assertEquals($request['order_tax_amount'], $klarnaSumTaxTotals);
    }

    public function isKlarnaOrderAmountShopOrderAmountSame(array $request, CartInterface $quote)
    {
        $klarnaOrderAmount = $request['order_amount'];
        $shopGrandTotal = round($quote->getBaseGrandTotal() * 100);

        $diff = abs($klarnaOrderAmount - $shopGrandTotal);
        $boolResult = in_array($diff, [0, 1]);

        Assert::assertTrue($boolResult);
    }

    public function isKlarnaOrderTaxAmountShopTaxSame(array $request, CartInterface $quote)
    {
        $klarnaOrderTaxAmount = $request['order_tax_amount'];
        $shopTaxGrandTotal = round($quote->getTotals()['tax']->getValue() * 100);

        Assert::assertEquals($klarnaOrderTaxAmount, $shopTaxGrandTotal);
    }
}
