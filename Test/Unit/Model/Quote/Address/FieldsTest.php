<?php
/**
 * Copyright 2025 Kustom AB
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Model\Quote\Address;

use Klarna\Base\Model\Quote\Address\Fields;
use Magento\Directory\Model\Region;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Covering the region mapping which broke order placement on Magento 2.4.8 for countries without regions
 *
 * @coversDefaultClass \Klarna\Base\Model\Quote\Address\Fields
 */
class FieldsTest extends TestCase
{
    /**
     * @var RegionFactory|MockObject
     */
    private $regionFactory;

    /**
     * @var Fields
     */
    private Fields $model;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->regionFactory = $this->createMock(RegionFactory::class);

        $dataObjectFactory = $this->createMock(DataObjectFactory::class);
        $dataObjectFactory->method('create')
            ->willReturnCallback(static fn(array $arguments = []) => new DataObject($arguments['data'] ?? []));

        $this->model = new Fields($this->regionFactory, $dataObjectFactory);
    }

    /**
     * The main regression: SE/NO/NL/DK/FI have no region, so region_id must be null and never the integer 0
     */
    public function testRegionIsNullWhenKustomSendsNoRegion(): void
    {
        $this->regionFactory->expects($this->never())->method('create');

        $data = $this->model->getQuoteAddressFieldsByKlarnaAddress($this->getAddress(['country' => 'se']));

        $this->assertNull($data['region_id']);
        $this->assertNull($data['region']);
        $this->assertSame('SE', $data['country_id']);
    }

    /**
     * An empty string must be handled exactly like a missing region
     */
    public function testRegionIsNullWhenRegionIsAnEmptyString(): void
    {
        $this->regionFactory->expects($this->never())->method('create');

        $data = $this->model->getQuoteAddressFieldsByKlarnaAddress(
            $this->getAddress(['country' => 'NO', 'region' => '   '])
        );

        $this->assertNull($data['region_id']);
        $this->assertNull($data['region']);
    }

    /**
     * A resolvable region must be normalised to the Magento region id and name
     */
    public function testRegionIsResolvedByCode(): void
    {
        $region = $this->createMock(Region::class);
        $region->method('loadByCode')->willReturnSelf();
        $region->method('getId')->willReturn(12);
        $region->method('getName')->willReturn('California');

        $this->regionFactory->method('create')->willReturn($region);

        $data = $this->model->getQuoteAddressFieldsByKlarnaAddress(
            $this->getAddress(['country' => 'US', 'region' => 'CA'])
        );

        $this->assertSame(12, $data['region_id']);
        $this->assertSame('California', $data['region']);
    }

    /**
     * Free text which cannot be mapped must be kept, but must not produce a fake region id
     */
    public function testUnresolvableRegionKeepsFreeTextWithoutRegionId(): void
    {
        $region = $this->createMock(Region::class);
        $region->method('loadByCode')->willReturnSelf();
        $region->method('loadByName')->willReturnSelf();
        $region->method('getId')->willReturn(null);

        $this->regionFactory->method('create')->willReturn($region);

        $data = $this->model->getQuoteAddressFieldsByKlarnaAddress(
            $this->getAddress(['country' => 'DE', 'region' => 'BE'])
        );

        $this->assertNull($data['region_id']);
        $this->assertSame('BE', $data['region']);
    }

    /**
     * The array key order has to stay stable because it is asserted with assertSame in the integration test
     */
    public function testFieldOrderIsStable(): void
    {
        $this->regionFactory->expects($this->never())->method('create');

        $data = $this->model->getQuoteAddressFieldsByKlarnaAddress($this->getAddress(['country' => 'SE']));

        $this->assertSame(
            [
                'lastname',
                'firstname',
                'email',
                'company',
                'prefix',
                'street',
                'postcode',
                'city',
                'region_id',
                'region',
                'telephone',
                'country_id'
            ],
            array_keys($data)
        );
    }

    /**
     * Building a Kustom address payload
     *
     * @param array $overrides
     * @return array
     */
    private function getAddress(array $overrides = []): array
    {
        return array_merge(
            [
                'given_name'     => 'Test',
                'family_name'    => 'Customer',
                'email'          => 'test@kustom.co',
                'street_address' => 'Test street 1',
                'postal_code'    => '11122',
                'city'           => 'Stockholm',
                'phone'          => '+46701234567',
                'country'        => 'SE'
            ],
            $overrides
        );
    }
}

