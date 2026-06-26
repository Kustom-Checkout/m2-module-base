<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

use Magento\Framework\Registry;
use Magento\Tax\Model\Calculation\RateFactory;
use Magento\Tax\Model\Calculation\RuleFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
/** @var RateFactory $rateFactory */
$rateFactory = $objectManager->create(RateFactory::class);
/** @var RuleFactory $ruleFactory */
$ruleFactory = $objectManager->create(RuleFactory::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$rate = $rateFactory->create()->load('US-AL-*-Rate-1', 'code');
if ($rate->getId()) {
    $rate->delete();
}

$rule = $ruleFactory->create()->load('36104 Test Rule', 'code');
if ($rule->getId()) {
    $rule->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
