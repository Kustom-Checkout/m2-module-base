<?php
/**
 * Copyright 2025 Kustom AB (Originally developed by Klarna Bank AB)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Quote\Address;

use Klarna\Base\Model\Quote\Address\Country;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\Store;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Customer\Model\Address as CustomerAddress;
use Magento\Sales\Model\Order;

/**
 * @coversDefaultClass \Klarna\Base\Model\Quote\Address\Country
 */
class CountryTest extends TestCase
{
    /**
     * @var Country
     */
    private Country $model;
    /**
     * @var Quote
     */
    private Quote $quote;
    /**
     * @var Address
     */
    private Address $billingAddress;
    /**
     * @var Address
     */
    private Address $shippingAddress;
    /**
     * @var CustomerAddress
     */
    private CustomerAddress $customerAddress;
    /**
     * @var Order
     */
    private Order $order;

    public function testGetCountryBillingAddressExistsAndCountryValueSetImpliesReturningBillingCountryValue(): void
    {
        $this->billingAddress->method('getCountry')
            ->willReturn('US');
        $this->billingAddress->method('getCountryId')
            ->willReturn('DE');

        static::assertEquals('US', $this->model->getCountry($this->quote));
    }

    public function testGetCountryBillingAddressExistsAndCountryValueNotSetButCountryIdImpliesReturningBillingCountryValue(): void
    {
        $this->billingAddress->method('getCountry')
            ->willReturn('');
        $this->billingAddress->method('getCountryId')
            ->willReturn('DE');

        static::assertEquals('DE', $this->model->getCountry($this->quote));
    }

    public function testGetCountryBillingAddressExistsAndCountryAndCountryIdEmptyButShippingCountrySetImpliesReturningShippingCountryValue(): void
    {
        $this->billingAddress->method('getCountry')
            ->willReturn('');
        $this->billingAddress->method('getCountryId')
            ->willReturn('');
        $this->quote->method('getShippingAddress')
            ->willReturn($this->shippingAddress);
        $this->shippingAddress->method('getCountry')
            ->willReturn('US');
        $this->shippingAddress->method('getCountryId')
            ->willReturn('DE');

        static::assertEquals('US', $this->model->getCountry($this->quote));
    }

    public function testGetCountryBillingAddressExistsAndCountryAndCountryIdEmptyButShippingCountryIdSetImpliesReturningShippingCountryIdValue(): void
    {
        $this->billingAddress->method('getCountry')
            ->willReturn('');
        $this->billingAddress->method('getCountryId')
            ->willReturn('');
        $this->quote->method('getShippingAddress')
            ->willReturn($this->shippingAddress);
        $this->shippingAddress->method('getCountry')
            ->willReturn('');
        $this->shippingAddress->method('getCountryId')
            ->willReturn('DE');

        static::assertEquals('DE', $this->model->getCountry($this->quote));
    }

    public function testGetCountryBillingAddressNotExistsButShippingCountrySetImpliesReturningShippingCountryValue(): void
    {
        $this->quote->method('getBillingAddress')
            ->willReturn(null);
        $this->quote->method('getShippingAddress')
            ->willReturn($this->shippingAddress);
        $this->shippingAddress->method('getCountry')
            ->willReturn('US');
        $this->shippingAddress->method('getCountryId')
            ->willReturn('DE');

        static::assertEquals('US', $this->model->getCountry($this->quote));
    }

    public function testGetCountryBillingAddressNotExistsButShippingCountryIdSetImpliesReturningShippingCountryIdValue(): void
    {
        $this->quote->method('getBillingAddress')
            ->willReturn(null);
        $this->quote->method('getShippingAddress')
            ->willReturn($this->shippingAddress);
        $this->shippingAddress->method('getCountry')
            ->willReturn('');
        $this->shippingAddress->method('getCountryId')
            ->willReturn('DE');

        static::assertEquals('DE', $this->model->getCountry($this->quote));
    }

    public function testGetCountryBillingAddressNotExistsAndShippingAddressNotExistsImpliesReturningDefaultCountryIdValue(): void
    {
        $this->quote->method('getBillingAddress')
            ->willReturn(null);
        $this->quote->method('getShippingAddress')
            ->willReturn(null);
        $this->dependencyMocks['directoryHelper']->method('getDefaultCountry')
            ->willReturn('DE');

        static::assertEquals('DE', $this->model->getCountry($this->quote));
    }

    public function testGetCountryBillingAddressNotExistsAndShippingAddressNotExistsAndDefaultEmptyImpliesReturningEmptyString(): void
    {
        $this->quote->method('getBillingAddress')
            ->willReturn(null);
        $this->quote->method('getShippingAddress')
            ->willReturn(null);
        $this->dependencyMocks['directoryHelper']->method('getDefaultCountry')
            ->willReturn('');

        static::assertEquals('', $this->model->getCountry($this->quote));
    }

    public function testGetCountryUsingOrderAsInputAndBillingAddressExistsImpliesReturningCountryId(): void
    {
        $this->order->method('getBillingAddress')
            ->willReturn($this->customerAddress);
        $this->customerAddress->method('getCountryId')
            ->willReturn('DE');

        static::assertEquals('DE', $this->model->getCountry($this->order));
    }

    public function testIsUsCountryReturnedUsBillingCountryImpliesTrue(): void
    {
        $this->billingAddress->method('getCountry')
            ->willReturn('US');

        static::assertTrue($this->model->isUsCountry($this->quote));
    }

    public function testIsUsCountryReturnedDeBillingCountryImpliesFalse(): void
    {
        $this->billingAddress->method('getCountry')
            ->willReturn('DE');

        static::assertFalse($this->model->isUsCountry($this->quote));
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(Country::class);
        $this->quote = $this->mockFactory->create(Quote::class);

        $this->billingAddress = $this->mockFactory->create(Address::class);
        $this->shippingAddress = $this->mockFactory->create(Address::class);

        $this->quote->method('getBillingAddress')
            ->willReturn($this->billingAddress);

        $store = $this->mockFactory->create(Store::class);
        $this->quote->method('getStore')
            ->willReturn($store);

        $this->order = $this->mockFactory->create(Order::class);
        $this->customerAddress = $this->mockFactory->create(CustomerAddress::class, [], ['getCountryId']);
    }
}
