<?php
/**
 * Copyright © Kustom AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Plugin\Sales;

use Klarna\Base\Api\OrderRepositoryInterface as KlarnaOrderRepositoryInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface as MagentoOrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Populating TMS (e.g. Ingrid) shipping option extension attributes (tos_id, carrier, pickup location name,
 * raw selected_shipping_option) onto the Magento sales order from the klarna_core_order entity.
 *
 * @internal
 */
class OrderRepositoryPlugin
{
    /**
     * @var KlarnaOrderRepositoryInterface
     */
    private KlarnaOrderRepositoryInterface $klarnaOrderRepository;
    /**
     * @var ExtensionAttributesFactory
     */
    private ExtensionAttributesFactory $extensionAttributesFactory;

    /**
     * @param KlarnaOrderRepositoryInterface $klarnaOrderRepository
     * @param ExtensionAttributesFactory     $extensionAttributesFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        KlarnaOrderRepositoryInterface $klarnaOrderRepository,
        ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->klarnaOrderRepository = $klarnaOrderRepository;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * Populate extension attributes after loading a single order
     *
     * @param OrderRepositoryInterface $subject
     * @param MagentoOrderInterface    $order
     * @return MagentoOrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(OrderRepositoryInterface $subject, MagentoOrderInterface $order): MagentoOrderInterface
    {
        $this->addExtensionAttributes($order);
        return $order;
    }

    /**
     * Populate extension attributes after loading a list of orders
     *
     * @param OrderRepositoryInterface $subject
     * @param SearchResultsInterface   $searchResult
     * @return SearchResultsInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        SearchResultsInterface $searchResult
    ): SearchResultsInterface {
        foreach ($searchResult->getItems() as $order) {
            $this->addExtensionAttributes($order);
        }

        return $searchResult;
    }

    /**
     * Adding the Kustom shipping option extension attributes to the given order
     *
     * @param MagentoOrderInterface $order
     */
    private function addExtensionAttributes(MagentoOrderInterface $order): void
    {
        try {
            $klarnaOrder = $this->klarnaOrderRepository->getByOrder($order);
        } catch (NoSuchEntityException $e) {
            return;
        }

        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionAttributesFactory->create(MagentoOrderInterface::class);
        }

        $extensionAttributes->setKustomTosId($klarnaOrder->getTosId());
        $extensionAttributes->setKustomShippingCarrier($klarnaOrder->getShippingCarrier());
        $extensionAttributes->setKustomShippingLocationName($klarnaOrder->getShippingLocationName());
        $extensionAttributes->setKustomSelectedShippingOption($klarnaOrder->getSelectedShippingOption());

        $order->setExtensionAttributes($extensionAttributes);
    }
}

