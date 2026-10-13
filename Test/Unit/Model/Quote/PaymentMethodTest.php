<?php
/**
 * Copyright 2025 Kustom AB (Originally developed by Klarna Bank AB)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Quote;

use Klarna\Base\Model\Quote\PaymentMethod;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Payment;

/**
 * @coversDefaultClass \Klarna\Base\Model\Quote\PaymentMethod
 */
class PaymentMethodTest extends TestCase
{
    /**
     * @var Quote
     */
    private Quote $magentoQuote;
    /**
     * @var QuoteAddress
     */
    private QuoteAddress $quoteShippingAddress;
    /**
     * @var QuoteAddress
     */
    private QuoteAddress $quoteBillingAddress;
    /**
     * @var PaymentMethod
     */
    private PaymentMethod $paymentMethod;
    /**
     * @var Payment
     */
    private Payment $quotePayment;

    public function testSetPaymentMethodSetPaymentOfVirtualQuote(): void
    {
        $this->magentoQuote->method('isVirtual')
            ->willReturn(true);
        $this->quoteShippingAddress->expects(static::never())
            ->method('setPaymentMethod')
            ->with('abc');
        $this->quoteBillingAddress->expects(static::once())
            ->method('setPaymentMethod');

        $this->paymentMethod->setPaymentMethod($this->magentoQuote, 'abc');
    }

    public function testSetPaymentMethodOfNotVirtualQuote(): void
    {
        $this->magentoQuote->method('isVirtual')
            ->willReturn(false);
        $this->quoteShippingAddress->expects(static::once())
            ->method('setPaymentMethod')
            ->with('abc');
        $this->quoteBillingAddress->expects(static::never())
            ->method('setPaymentMethod');

        $this->paymentMethod->setPaymentMethod($this->magentoQuote, 'abc');
    }

    protected function setUp(): void
    {
        $this->paymentMethod = parent::setUpMocks(PaymentMethod::class);

        $this->quoteBillingAddress = $this->mockFactory->create(QuoteAddress::class, [], ['setPaymentMethod']);
        $this->quoteShippingAddress = $this->mockFactory->create(QuoteAddress::class, [], ['setPaymentMethod']);
        $this->quotePayment = $this->mockFactory->create(Payment::class);

        $this->magentoQuote = $this->mockFactory->create(
            Quote::class,
            [
                'getBillingAddress',
                'getShippingAddress',
                'getPayment',
                'isVirtual'
            ]
        );
        $this->magentoQuote->method('getBillingAddress')
            ->willReturn($this->quoteBillingAddress);
        $this->magentoQuote->method('getShippingAddress')
            ->willReturn($this->quoteShippingAddress);
        $this->magentoQuote->method('getPayment')
            ->willReturn($this->quotePayment);
    }
}