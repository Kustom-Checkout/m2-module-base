<?php
/**
 * Copyright © Kustom AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Plugin\Sales;

use Magento\Sales\Api\Data\OrderExtensionInterface;

/**
 * Test double declaring the Kustom extension attribute setters explicitly.
 *
 * The real setters are generated from etc/extension_attributes.xml into
 * Magento\Sales\Api\Data\OrderExtensionInterface, which is not guaranteed to contain them in a unit test
 * context (no di:compile / generated code). Declaring them here keeps the mock stable and independent of
 * code generation, replacing the MockBuilder::addMethods() approach that was removed in PHPUnit 12.
 */
interface KustomOrderExtensionTestInterface extends OrderExtensionInterface
{
    /**
     * Setting the TMS TOS ID
     *
     * @param string|null $kustomTosId
     * @return $this
     */
    public function setKustomTosId($kustomTosId);

    /**
     * Setting the shipping carrier
     *
     * @param string|null $kustomShippingCarrier
     * @return $this
     */
    public function setKustomShippingCarrier($kustomShippingCarrier);

    /**
     * Setting the pickup location name
     *
     * @param string|null $kustomShippingLocationName
     * @return $this
     */
    public function setKustomShippingLocationName($kustomShippingLocationName);

    /**
     * Setting the raw selected shipping option
     *
     * @param string|null $kustomSelectedShippingOption
     * @return $this
     */
    public function setKustomSelectedShippingOption($kustomSelectedShippingOption);
}

