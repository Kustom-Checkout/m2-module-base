<?php
/**
 * Copyright 2025 Kustom AB (Originally developed by Klarna Bank AB)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Quote\Address;

use Magento\Directory\Model\Region;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * @internal
 */
class Fields
{
    /**
     * @var RegionFactory
     */
    private RegionFactory $regionFactory;
    /**
     * @var DataObjectFactory
     */
    private DataObjectFactory $dataObjectFactory;

    /**
     * @param RegionFactory $regionFactory
     * @param DataObjectFactory $dataObjectFactory
     * @codeCoverageIgnore
     */
    public function __construct(RegionFactory $regionFactory, DataObjectFactory $dataObjectFactory)
    {
        $this->regionFactory = $regionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Getting back the quote address fields filled by the Klarna address
     *
     * @param array $klarnaAddressInput
     * @return array
     */
    public function getQuoteAddressFieldsByKlarnaAddress(array $klarnaAddressInput): array
    {
        $klarnaAddressInstance = $this->dataObjectFactory->create(['data' => $klarnaAddressInput]);
        $country = strtoupper((string)$klarnaAddressInstance->getCountry());
        $regionData = $this->getRegionData($klarnaAddressInstance->getRegion(), $country);

        $data = [
            AddressInterface::KEY_LASTNAME      => $klarnaAddressInstance->getFamilyName(),
            AddressInterface::KEY_FIRSTNAME     => $klarnaAddressInstance->getGivenName(),
            AddressInterface::KEY_EMAIL         => $klarnaAddressInstance->getEmail(),
            AddressInterface::KEY_COMPANY       => $klarnaAddressInstance->getOrganizationName(),
            AddressInterface::KEY_PREFIX        => $klarnaAddressInstance->getTitle(),
            AddressInterface::KEY_STREET        => $this->getStreetData($klarnaAddressInstance),
            AddressInterface::KEY_POSTCODE      => $klarnaAddressInstance->getPostalCode(),
            AddressInterface::KEY_CITY          => $klarnaAddressInstance->getCity(),
            AddressInterface::KEY_REGION_ID     => $regionData[AddressInterface::KEY_REGION_ID],
            AddressInterface::KEY_REGION        => $regionData[AddressInterface::KEY_REGION],
            AddressInterface::KEY_TELEPHONE     => $klarnaAddressInstance->getPhone(),
            AddressInterface::KEY_COUNTRY_ID    => $country
        ];

        if ($klarnaAddressInstance->hasCustomerDob()) {
            $data['dob'] = $klarnaAddressInstance->getCustomerDob();
        }

        if ($klarnaAddressInstance->hasCustomerGender()) {
            $data['gender'] = $klarnaAddressInstance->getCustomerGender();
        }

        return $data;
    }

    /**
     * Building the region fields for the quote address
     *
     * Kustom does not return a region for countries which have no region/state concept (for example SE, NO, NL, FI,
     * DK, DE). Magento 2.4.8 changed the way regions are exported from the quote address
     * (\Magento\Quote\Model\Quote\Address::exportCustomerAddress() nests the region data into a RegionInterface), which
     * makes an empty string or the integer 0 an invalid value. Because of that we have to send an explicit null instead
     * of casting a missing region to (int) 0, otherwise the order placement fails for those countries.
     *
     * @param mixed $region
     * @param string $country
     * @return array
     */
    private function getRegionData($region, string $country): array
    {
        $region = is_string($region) ? trim($region) : $region;

        if ($region === null || $region === '' || $country === '') {
            return [
                AddressInterface::KEY_REGION_ID => null,
                AddressInterface::KEY_REGION    => null
            ];
        }

        $regionModel = $this->loadRegion((string)$region, $country);

        if ($regionModel === null || !$regionModel->getId()) {
            /**
             * The country does have regions but Kustom returned a free text value which we cannot map. We keep the
             * free text so that the information is not lost, but we must not invent a region id for it.
             */
            return [
                AddressInterface::KEY_REGION_ID => null,
                AddressInterface::KEY_REGION    => (string)$region
            ];
        }

        return [
            AddressInterface::KEY_REGION_ID => (int)$regionModel->getId(),
            AddressInterface::KEY_REGION    => (string)$regionModel->getName()
        ];
    }

    /**
     * Loading the region by code and falling back to the region name
     *
     * @param string $region
     * @param string $country
     * @return Region|null
     */
    private function loadRegion(string $region, string $country): ?Region
    {
        try {
            $regionModel = $this->regionFactory->create()->loadByCode($region, $country);

            if ($regionModel->getId()) {
                return $regionModel;
            }

            $regionModel = $this->regionFactory->create()->loadByName($region, $country);

            return $regionModel->getId() ? $regionModel : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Getting back the street data
     *
     * @param DataObject $klarnaAddressData
     * @return array
     */
    private function getStreetData(DataObject $klarnaAddressData): array
    {
        return array_filter(
            [
                $klarnaAddressData->getStreetAddress() . $klarnaAddressData->getHouseExtension(),
                $klarnaAddressData->getData('street_address2'),
            ]
        );
    }
}
