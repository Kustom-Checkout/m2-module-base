<?php
/**
 * Copyright © Kustom AB (Originally developed by Klarna Bank AB)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Api;

use Klarna\Orderlines\Model\Fpt\Calculator;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Klarna\Orderlines\Model\Container\Parameter;
use Klarna\Orderlines\Model\Container\DataHolder;

/**
 * Processing the order lines based on the given object
 *
 * @internal
 */
class OrderLineProcessor
{

    /**
     * Holding all data from a quote or invoice or credit memo
     *
     * @var DataHolder $dataHolder
     */
    private $dataHolder;

    /** @var Calculator $calculator */
    private Calculator $calculator;

    /**
     * @param DataHolder $dataHolder
     * @param Calculator $calculator
     * @codeCoverageIgnore
     */
    public function __construct(DataHolder $dataHolder, Calculator $calculator)
    {
        $this->dataHolder = $dataHolder;
        $this->calculator = $calculator;
    }

    /**
     * Processing the order lines based on the given quote
     *
     * @param Parameter $parameter
     * @param CartInterface $quote
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processByQuote(Parameter $parameter, CartInterface $quote)
    {
        $this->fillBasicDataHolder(
            $quote,
            $quote->getTotals(),
            $quote->getAllItems(),
            $quote->getTotals()['tax']->getValue()
        );
        $this->dataHolder->setVirtualFlag($quote->isVirtual());

        $parameter->setVirtualFlag($this->dataHolder->isVirtual());
        $parameter->setStore($quote->getStore());
        foreach ($parameter->getOrderlineItemEntities() as $model) {
            $model->collectPrePurchase($parameter, $this->dataHolder, $quote);
        }

        return $this;
    }

    /**
     * Processing the order lines based on the given invoice
     *
     * @param Parameter $parameter
     * @param InvoiceInterface $invoice
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processByInvoice(Parameter $parameter, InvoiceInterface $invoice)
    {
        $this->fillDataHolderPostPurchase($invoice, $parameter);

        $parameter->setStore($invoice->getStore());
        foreach ($parameter->getOrderlineItemEntities() as $model) {
            $model->collectPostPurchase($parameter, $this->dataHolder, $invoice->getOrder());
        }

        return $this;
    }

    /**
     * Processing the order lines based on the given invoice
     *
     * @param Parameter $parameter
     * @param CreditmemoInterface $creditMemo
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processByCreditMemo(Parameter $parameter, CreditmemoInterface $creditMemo)
    {
        $this->fillDataHolderPostPurchase($creditMemo, $parameter);

        $parameter->setStore($creditMemo->getStore());
        foreach ($parameter->getOrderlineItemEntities() as $model) {
            $model->collectPostPurchase($parameter, $this->dataHolder, $creditMemo->getOrder());
        }

        return $this;
    }

    /**
     * Build items array from order items
     *
     * @param array $orderItems
     * @return array
     */
    private function getItemsFromPurchaseObject(array $orderItems)
    {
        $items = [];
        foreach ($orderItems as $item) {
            $orderItem = $item->getOrderItem();
            $orderItem->setCurrentInvoiceRefundItemQty($item->getQty());
            $orderItem->setCurrentInvoiceRefundItemBaseRowTotal($item->getBaseRowTotal());
            $orderItem->setCurrentInvoiceRefundItemBaseRowTotalInclTax($item->getBaseRowTotalInclTax());
            $orderItem->setCurrentInvoiceRefundItemBaseTaxAmount($item->getBaseTaxAmount());
            $orderItem->setCurrentInvoiceRefundItemBaseDiscountAmount($item->getBaseDiscountAmount());
            $orderItem->setCurrentInvoiceRefundItemBaseDiscountTaxCompensationAmount(
                $item->getBaseDiscountTaxCompensationAmount()
            );
            $items[] = $orderItem;
        }
        return $items;
    }

    /**
     * Filling the data holder on a post purchase step
     *
     * @param ExtensibleDataInterface $data
     * @param Parameter $orderLineParameters
     */
    private function fillDataHolderPostPurchase(ExtensibleDataInterface $data, Parameter $orderLineParameters): void
    {
        $totals = $data->getTotals();
        if ($totals === null) {
            $totals = [];
        }

        $this->fillBasicDataHolder(
            $data,
            $totals,
            $this->getItemsFromPurchaseObject($data->getAllItems()),
            (float) $data->getBaseTaxAmount()
        );

        $this->dataHolder->setVirtualFlag((bool) $data->getOrder()->getIsVirtual());
        $orderLineParameters->setVirtualFlag($this->dataHolder->isVirtual());

        $this->dataHolder->setBaseShippingInclTax($data->getBaseShippingInclTax())
            ->setShippingAmount($data->getShippingTaxAmount())
            ->setShippingHiddenTaxAmount($data->getShippingHiddenTaxAmount());
    }

    /**
     * Filling the data holder with basic information
     *
     * @param ExtensibleDataInterface $data
     * @param array $totals
     * @param array $items
     * @param float $totalTax
     */
    private function fillBasicDataHolder(
        ExtensibleDataInterface $data,
        array $totals,
        array $items,
        float $totalTax
    ): void {
        $this->dataHolder->setTotals($totals)
            ->setUsedCustomerBalanceAmount($data->getCustomerBalanceAmountUsed())
            ->setShippingAddress($data->getShippingAddress())
            ->setBillingAddress($data->getBillingAddress())
            ->setStore($data->getStore())
            ->setFlatItems($data->getItems())
            ->setDiscountAmount($data->getDiscountAmount())
            ->setCouponCode($data->getCouponCode())
            ->setDiscountDescription($data->getDiscountDescription())
            ->setBaseSubtotalWithDiscount($data->getBaseSubtotalWithDiscount())
            ->setBaseSubtotal($data->getBaseSubtotal())
            ->setCustomerTaxClassId($data->getCustomerTaxClassId())
            ->setUsedGiftCardAmount($data->getGiftCardsAmountUsed())
            ->setItems($items)
            ->setGiftWrapId($data->getGwId())
            ->setGiftWrapBasePrice($data->getGwBasePrice())
            ->setTotalTax($totalTax)
            ->setFptTax($this->calculator->getFptData($data));
    }
}
