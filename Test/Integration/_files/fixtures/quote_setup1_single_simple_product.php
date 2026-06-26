<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Quote\Api\Data\AddressInterface;

Resolver::getInstance()->requireDataFixture('Klarna_Base::Test/Integration/_files/fixtures/product_simple.php');
Resolver::getInstance()->requireDataFixture('Klarna_Base::Test/Integration/_files/fixtures/tax_rule_us_postal_36104.php');

$objectManager = Bootstrap::getObjectManager();

/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
/** @var Product $productLoader */
$productLoader = $objectManager->get(Product::class);
/** @var QuoteFactory $mQuoteFactory */
$mQuoteFactory = $objectManager->get(QuoteFactory::class);
/** @var QuoteManagement $quoteManagement */
$quoteManagement = $objectManager->get(QuoteManagement::class);
/** @var CustomerFactory $customerFactory */
$customerFactory = $objectManager->get(CustomerFactory::class);
/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->get(CustomerRepositoryInterface::class);
/** @var \Klarna\Kco\Model\QuoteFactory $kQuoteFactory */
$kQuoteFactory = $objectManager->create(\Klarna\Kco\Model\QuoteFactory::class);

$store = $storeManager->getStore();
$websiteId = $storeManager->getStore()->getWebsiteId();
$customer = $customerFactory->create();
$customer->setWebsiteId($websiteId)
    ->setStore($store)
    ->setFirstname('Jhon')
    ->setLastname('Deo')
    ->setEmail('sdfds@sdfsd.de')
    ->setPassword("password");
$customer->save();
$customer = $customerRepository->getById($customer->getEntityId());

$product = $productLoader->load(99999);
$quote = $mQuoteFactory->create();
$quote->setStore($store);
$quote->setGlobalCurrencyCode('USD')
    ->setBaseCurrencyCode('USD')
    ->setStoreCurrencyCode('USD')
    ->setQuoteCurrencyCode('USD');
$quote->assignCustomer($customer);
$quote->setSendConfirmation(1);
$quote->addProduct($product, 1);

$addressData = [
    AddressInterface::KEY_TELEPHONE => '3468676',
    AddressInterface::KEY_POSTCODE => '36104',
    AddressInterface::KEY_COUNTRY_ID => 'US',
    AddressInterface::KEY_CITY => 'CityM',
    AddressInterface::KEY_COMPANY => 'CompanyName',
    AddressInterface::KEY_STREET => 'Green str, 67',
    AddressInterface::KEY_LASTNAME => 'Smith',
    AddressInterface::KEY_FIRSTNAME => 'John',
    AddressInterface::KEY_REGION => 'CA',
    AddressInterface::KEY_REGION_ID => '12',
    AddressInterface::KEY_EMAIL => 'any_mail@mail.me'
];

$billingAddress = $quote->getBillingAddress()->addData($addressData);
$shippingAddress = $quote->getShippingAddress()->addData($addressData);

$shippingAddress->setCollectShippingRates(true)
    ->collectShippingRates()
    ->setShippingMethod('flatrate_flatrate')
    ->setPaymentMethod('checkmo');
$quote->setPaymentMethod('checkmo');
$quote->setInventoryProcessed(false);
$quote->save();
$quote->getPayment()->importData(array('method' => 'checkmo'));

$quote->setReservedOrderId('100000001');

$quote->setTotalsCollectedFlag(false);
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->collectTotals()->save();

$klarnaQuote = $kQuoteFactory->create();
$klarnaQuote->setQuoteId($quote->getId());
$klarnaQuote->setKlarnaCheckoutId('123456-1234-1234-1234-1234567890');
$klarnaQuote->setIsActive(true);
$klarnaQuote->save();
