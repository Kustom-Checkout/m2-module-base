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
abstract class ValidatorAbstract
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

    public function getFullTotalTaxAmount(array $orderlines): float
    {
        $klarnaTax = 0;
        foreach ($orderlines as $item) {
            if ($item['type'] === 'sales_tax') {
                return round($item['total_amount']);
            }
            $klarnaTax += $item['total_tax_amount'];
        }

        return round($klarnaTax);
    }

    public function getFullTotalAmount(array $orderlines): float
    {
        $klarnaTax = 0;
        foreach ($orderlines as $item) {
            $klarnaTax += $item['total_amount'];
        }

        return round($klarnaTax);
    }

    public function isKlarnaShippingOrderlineItemMissing(array $orderlines)
    {
        $result = true;
        foreach ($orderlines as $item) {
            if ($item['type'] === 'shipping_fee') {
                $result = false;
            }
        }

        Assert::assertTrue($result, "Shipping orderline item is missing");
    }

    public function getUsTaxOrderlineItem(array $orderlines)
    {
        foreach ($orderlines as $item) {
            if ($item['type'] === 'sales_tax') {
                return $item;
            }
        }

        throw new \Exception('No item found!');
    }

    public function getShippingOrderlineItem(array $orderlines)
    {
        foreach ($orderlines as $item) {
            if ($item['type'] === 'shipping_fee') {
                return $item;
            }
        }

        throw new \Exception('No item found!');
    }

    public function getTaxOrderlineItem(array $orderlines)
    {
        foreach ($orderlines as $item) {
            if ($item['type'] === 'sales_tax') {
                return $item;
            }
        }

        throw new \Exception('No item found!');
    }

    public function getAllProductOrderlineItems(array $orderlines)
    {
        $items = [];
        foreach ($orderlines as $item) {
            if (in_array($item['type'], ['digital', 'physical'])) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public function hasEachOrderlineItemNonZeroTaxAmount(array $request)
    {
        foreach ($request['order_lines'] as $item) {
            Assert::assertNotEmpty($item['total_tax_amount'], 'Total tax amount is empty for the orderline item');
        }
    }
}
