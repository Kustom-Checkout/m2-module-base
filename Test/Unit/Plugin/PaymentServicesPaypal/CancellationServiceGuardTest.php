<?php
/**
 * Copyright 2025 Kustom AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Plugin\PaymentServicesPaypal;

use Klarna\Base\Plugin\PaymentServicesPaypal\CancellationServiceGuard;
use Klarna\Logger\Api\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Payment;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Klarna\Base\Plugin\PaymentServicesPaypal\CancellationServiceGuard
 */
class CancellationServiceGuardTest extends TestCase
{
    /**
     * @var CartRepositoryInterface|MockObject
     */
    private $quoteRepository;

    /**
     * @var CancellationServiceGuard
     */
    private CancellationServiceGuard $model;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->quoteRepository = $this->createMock(CartRepositoryInterface::class);

        $this->model = new CancellationServiceGuard(
            $this->quoteRepository,
            $this->createMock(LoggerInterface::class),
            ['klarna_kco', 'klarna_kp', 'klarna']
        );
    }

    /**
     * The cancellation service must not run for Kustom payment methods
     */
    public function testKustomPaymentMethodIsSkipped(): void
    {
        $this->quoteRepository->method('get')->willReturn($this->getQuote('klarna_kco'));

        $result = $this->model->aroundExecute(
            new \stdClass(),
            static fn() => self::fail('The cancellation service must not be called for Kustom orders'),
            1166979
        );

        $this->assertFalse($result);
    }

    /**
     * An unloadable quote must not bubble up "No such entity with cartId = X" and mask the real exception
     */
    public function testUnloadableQuoteIsSwallowed(): void
    {
        $this->quoteRepository->method('get')
            ->willThrowException(new NoSuchEntityException(__('No such entity with cartId = %1', 1166979)));

        $result = $this->model->aroundExecute(
            new \stdClass(),
            static fn() => self::fail('The cancellation service must not be called for unloadable quotes'),
            1166979
        );

        $this->assertFalse($result);
    }

    /**
     * Non Kustom payment methods must keep the original Magento behaviour
     */
    public function testOtherPaymentMethodsAreDelegated(): void
    {
        $this->quoteRepository->method('get')->willReturn($this->getQuote('payment_services_paypal_hosted_fields'));

        $result = $this->model->aroundExecute(new \stdClass(), static fn(int $cartId) => true, 55);

        $this->assertTrue($result);
    }

    /**
     * Building a quote mock with the given payment method
     *
     * @param string $method
     * @return Quote|MockObject
     */
    private function getQuote(string $method)
    {
        $payment = $this->createMock(Payment::class);
        $payment->method('getMethod')->willReturn($method);

        $quote = $this->createMock(Quote::class);
        $quote->method('getPayment')->willReturn($payment);

        return $quote;
    }
}

