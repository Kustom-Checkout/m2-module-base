<?php
/**
 * Copyright 2025 Kustom AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Plugin\PaymentServicesPaypal;

use Klarna\Logger\Api\LoggerInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Protecting the order placement against the payment services cancellation service
 *
 * Magento\PaymentServicesPaypal\Plugin\OrderCancellation is an around plugin on
 * Magento\Quote\Api\CartManagementInterface::placeOrder(). It therefore also wraps the order placement done by the
 * Kustom modules. On any exception it calls CancellationService::execute(), which loads the quote through the cart
 * repository *before* it checks whether the payment method is one it is responsible for.
 *
 * When the quote can no longer be loaded - which happens when the shop order was already created by a concurrent push
 * request, or when the quote was deactivated during the failed placement - that repository call throws
 * "No such entity with cartId = X" from inside the catch block. The original exception is then lost and merchants only
 * see the misleading cart id error in var/log/klarna.log, which makes the real problem impossible to diagnose.
 *
 * This plugin short circuits the cancellation service for Kustom payment methods and for quotes which cannot be
 * loaded, so that the original exception keeps bubbling up untouched.
 *
 * @internal
 */
class CancellationServiceGuard
{
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $quoteRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var string[]
     */
    private array $methodCodes;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param LoggerInterface $logger
     * @param string[] $methodCodes
     * @codeCoverageIgnore
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        LoggerInterface $logger,
        array $methodCodes = []
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->methodCodes = $methodCodes;
    }

    /**
     * Skipping the cancellation service for Kustom orders and for quotes which cannot be loaded
     *
     * @param object $subject
     * @param callable $proceed
     * @param int $cartId
     * @return bool
     */
    public function aroundExecute($subject, callable $proceed, int $cartId): bool
    {
        $paymentMethod = $this->getQuotePaymentMethod($cartId);

        if ($paymentMethod === null) {
            $this->logger->info(
                'Skipping the payment services cancellation service because quote ' . $cartId
                . ' could not be loaded. The original order placement exception is kept.'
            );

            return false;
        }

        if (in_array($paymentMethod, $this->methodCodes, true)) {
            $this->logger->info(
                'Skipping the payment services cancellation service for Kustom payment method "' . $paymentMethod
                . '" on quote ' . $cartId
            );

            return false;
        }

        return (bool)$proceed($cartId);
    }

    /**
     * Getting back the payment method of the quote, null when the quote cannot be loaded
     *
     * @param int $cartId
     * @return string|null
     */
    private function getQuotePaymentMethod(int $cartId): ?string
    {
        try {
            $payment = $this->quoteRepository->get($cartId)->getPayment();

            return $payment === null ? '' : (string)$payment->getMethod();
        } catch (\Exception $e) {
            return null;
        }
    }
}

