<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Block\Info;

use Klarna\Base\Model\System\MerchantPortal;
use Klarna\Base\Model\OrderRepository;
use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\State;
use Klarna\Base\Api\OrderInterface;
use Magento\Payment\Block\Info;
use Magento\Sales\Api\Data\OrderInterface as MagentoOrder;

/**
 *
 * @internal
 */
class Klarna extends Info
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * Klarna Order Repository
     *
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var MerchantPortal
     */
    private $merchantPortal;
    /**
     * @var State
     */
    private $appState;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var OrderInterface
     */
    private $klarnaOrder;

    /**
     * @param Context           $context
     * @param OrderRepository   $orderRepository
     * @param MerchantPortal    $merchantPortal
     * @param DataObjectFactory $dataObjectFactory
     * @param UrlInterface      $urlBuilder
     * @param array             $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        OrderRepository $orderRepository,
        MerchantPortal $merchantPortal,
        DataObjectFactory $dataObjectFactory,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->orderRepository   = $orderRepository;
        $this->_template         = 'Klarna_Base::payment/info.phtml';
        $this->merchantPortal    = $merchantPortal;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->urlBuilder        = $urlBuilder;
        $this->appState          = $context->getAppState();
    }

    /**
     * Get specific information for the invoice pdf.
     *
     * @return array
     */
    public function getSpecificInformation(): array
    {
        $result = $this->getDisplayedInformation();
        $result->unsetData((string)__('Merchant Portal'));
        $result->unsetData((string)__('Logs'));
        $result->unsetData((string)__('Authorized Payment Method'));

        return $result->getData();
    }

    /**
     * Get specific information for the payment section in the admin order page.
     *
     * @return array
     */
    public function getFullSpecificInformation(): array
    {
        $result = $this->getDisplayedInformation();
        return $result->getData();
    }

    /**
     * Getting all displayed information
     *
     * @return DataObject
     * @throws LocalizedException
     */
    private function getDisplayedInformation(): DataObject
    {
        $data = parent::getSpecificInformation();
        $transport = $this->dataObjectFactory->create(['data' => $data]);
        $info = $this->getInfo();
        $order = $info->getOrder();
        try {
            $this->klarnaOrder = $this->orderRepository->getByOrder($order);

            if ($this->klarnaOrder->getId() && $this->klarnaOrder->getKlarnaOrderId()) {
                $transport->setData((string)__('Order ID'), $this->klarnaOrder->getKlarnaOrderId());

                $this->addReservationToDisplay($transport, $this->klarnaOrder);
                $this->addMerchantPortalLinkToDisplay($transport, $order, $this->klarnaOrder);
                $this->addLogLinkToDisplay($transport, $this->klarnaOrder);
                $this->addAuthorizedPaymentMethodToDisplay($transport, $this->klarnaOrder);
            }
        } catch (NoSuchEntityException $e) {
            $transport->setData((string)__('Error'), $e->getMessage());
        }

        $klarnaReferenceId = $info->getAdditionalInformation('klarna_reference');
        if ($klarnaReferenceId) {
            $transport->setData((string)__('Reference'), $klarnaReferenceId);
        }

        $this->addInvoicesToDisplay($transport, $order);

        return $transport;
    }

    /**
     * Add Klarna Reservation ID to order view
     *
     * @param DataObject     $transport
     * @param OrderInterface $klarnaOrder
     */
    private function addReservationToDisplay(DataObject $transport, OrderInterface $klarnaOrder)
    {
        if ($klarnaOrder->getReservationId()
            && $klarnaOrder->getReservationId() != $klarnaOrder->getKlarnaOrderId()
        ) {
            $transport->setData((string)__('Reservation'), $klarnaOrder->getReservationId());
        }
    }

    /**
     * Add Klarna Merchant Portal link to order view
     *
     * @param DataObject     $transport
     * @param MagentoOrder   $order
     * @param OrderInterface $klarnaOrder
     */
    private function addMerchantPortalLinkToDisplay(
        DataObject $transport,
        MagentoOrder $order,
        OrderInterface $klarnaOrder
    ) {
        //get merchant link only in admin
        if ($this->appState->getAreaCode() === Area::AREA_ADMINHTML) {
            $merchantPortalLink = $this->merchantPortal->getOrderMerchantPortalLink($order, $klarnaOrder);
            if ($merchantPortalLink) {
                $transport->setData(
                    (string)__('Merchant Portal'),
                    $this->merchantPortal->getOrderMerchantPortalLink($order, $klarnaOrder)
                );
            }
        }
    }

    /**
     * Add log link to order view
     *
     * @param DataObject $transport
     * @param OrderInterface $klarnaOrder
     * @throws LocalizedException
     */
    private function addLogLinkToDisplay(
        DataObject $transport,
        OrderInterface $klarnaOrder
    ) {
        //get link only in admin
        if ($this->appState->getAreaCode() === Area::AREA_ADMINHTML) {
            $url = $this->urlBuilder->getUrl('klarna/index/logs', [
                'klarna_id' => $klarnaOrder->getSessionId() ?: $klarnaOrder->getKlarnaOrderId()
            ]);
            $transport->setData(
                (string)__('Logs'),
                $url
            );
        }
    }

    /**
     * Add invoices to order view
     *
     * @param DataObject $transport
     * @param MagentoOrder $order
     */
    private function addInvoicesToDisplay(DataObject $transport, MagentoOrder $order)
    {
        $invoices = $order->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            if ($invoice->getTransactionId()) {
                $invoiceKey = (string)__('Invoice ID (#%1)', $invoice->getIncrementId());
                $transport->setData($invoiceKey, $invoice->getTransactionId());
            }
        }
    }

    /**
     * Check if string is a url
     *
     * @param string $string
     * @return bool
     */
    public function isStringUrl($string)
    {
        return (bool)filter_var($string, FILTER_VALIDATE_URL);
    }

    /**
     * Returning the logo url
     *
     * @return string
     */
    public function getLogoUrl(): string
    {
        return 'https://cdn.kustom.co/assets/badges/kustom_logo_black.png';
    }

    /**
     * Returns true if the order is a B2B order
     *
     * @return bool
     */
    public function isB2bOrder(): bool
    {
        if (!$this->klarnaOrder) {
            return false;
        }

        return $this->klarnaOrder->isB2b();
    }

    /**
     * Add authorized payment method to order view
     *
     * @param DataObject $transport
     * @param OrderInterface $klarnaOrder
     *
     * @return void
     *
     * @throws LocalizedException
     */
    private function addAuthorizedPaymentMethodToDisplay(
        DataObject $transport,
        OrderInterface $klarnaOrder
    ): void {
        //get only in admin
        if ($this->appState->getAreaCode() === Area::AREA_ADMINHTML &&
            $klarnaOrder->getAuthorizedPaymentMethod()
        ) {
            $transport->setData(
                (string)__('Authorized Payment Method'),
                strtoupper($klarnaOrder->getAuthorizedPaymentMethod())
            );
        }
    }
}
