<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Model\Quote\Address;

use Klarna\Base\Model\Quote\Address\Fields;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Klarna\Base\Model\Quote\Address\Fields
 */
class FieldsTest extends TestCase
{
    /**
     * @var ObjectManagerInterface|null
     */
    private ?ObjectManagerInterface $objectManager = null;

    /**
     * @var Fields|null
     */
    private ?Fields $model = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create(Fields::class);
    }

    #[DataProvider('differentAddressInputDataProvider')]
    /**
     * @dataProvider differentAddressInputDataProvider
     *
     * @param $klarnaAddressInput
     * @param $expected
     * @return void
     */
    public function testGetQuoteAddressFieldsByKlarnaAddressCheckCorrespondingInputOutput(
        $klarnaAddressInput,
        $expected
    ): void {
        $data = $this->model->getQuoteAddressFieldsByKlarnaAddress($klarnaAddressInput);
        $this->assertSame($expected, $data);
    }

    #[DataProvider('addressInputDataProviderWithStreetAddress2')]
    /**
     * @dataProvider addressInputDataProviderWithStreetAddress2
     *
     * @param $klarnaAddressInput
     * @param $expected
     * @return void
     */
    public function testGetQuoteAddressStreet2FieldsByKlarnaAddressCheckCorrespondingInputOutput(
        $klarnaAddressInput,
        $expected
    ): void {
        $data = $this->model->getQuoteAddressFieldsByKlarnaAddress($klarnaAddressInput);
        $this->assertSame($expected, $data);
    }

    public static function addressInputDataProviderWithStreetAddress2(): array
    {
        $basicAddressInput = [
            'given_name' => 'my firstname',
            'family_name' => 'my lastname',
            'country' => 'DE',
            'email' => 'myEmailAddress@klarna.com',
            'organization_name' => 'Klarna',
            'title' => 'Mr.',
            'street_address' => 'my street address',
            'house_extension' => '10',
            'street_address2' => 'my street address 2',
            'ramin' => 'my ramin',
            'postal_code' => '10101',
            'city' => 'BE',
            'region' => 'BE',
            'phone' => '+491111111111',
        ];

        $basicExpectedOutput = [
            'lastname' => 'my lastname',
            'firstname' => 'my firstname',
            'email' => 'myEmailAddress@klarna.com',
            'company' => 'Klarna',
            'prefix' => 'Mr.',
            'street' => [
                'my street address10',
                'my street address 2'
            ],
            'postcode' => '10101',
            'city' => 'BE',
            'region_id' => 0,
            'region' => 'BE',
            'telephone' => '+491111111111',
            'country_id' => 'DE',
        ];

        return [
            'inputWithStreetAddress2' => [
                $basicAddressInput,
                $basicExpectedOutput,
            ]
        ];
    }

    public static function differentAddressInputDataProvider(): array
    {
        $basicAddressInput = [
            'given_name' => 'my firstname',
            'family_name' => 'my lastname',
            'country' => 'DE',
            'email' => 'myEmailAddress@klarna.com',
            'organization_name' => 'Klarna',
            'title' => 'Mr.',
            'street_address' => 'my street address',
            'house_extension' => '10',
            'ramin' => 'my ramin',
            'postal_code' => '10101',
            'city' => 'BE',
            'region' => 'BE',
            'phone' => '+491111111111',
        ];

        $basicExpectedOutput = [
            'lastname' => 'my lastname',
            'firstname' => 'my firstname',
            'email' => 'myEmailAddress@klarna.com',
            'company' => 'Klarna',
            'prefix' => 'Mr.',
            'street' => [
                'my street address10',
            ],
            'postcode' => '10101',
            'city' => 'BE',
            'region_id' => 0,
            'region' => 'BE',
            'telephone' => '+491111111111',
            'country_id' => 'DE',
        ];

        $addressInputContainsDOB = array_merge($basicAddressInput, [
            'customer_dob' => '02/02/2002',
        ]);
        $expectedOutputContainsDOB = array_merge($basicExpectedOutput, ['dob' => '02/02/2002']);

        $addressInputContainsGender = array_merge($basicAddressInput, [
            'customer_gender' => 'male'
        ]);
        $expectedOutputContainsGender = array_merge($basicExpectedOutput, ['gender' => 'male']);

        $addressInputContainsSomeMissingValues = $basicAddressInput;
        unset(
            $addressInputContainsSomeMissingValues['given_name'],
            $addressInputContainsSomeMissingValues['email']
        );

        $expectedSomeMissingValues = array_merge($basicExpectedOutput, ['firstname' => null, 'email' => null]);

        return [
            'basicInput' => [
                $basicAddressInput,
                $basicExpectedOutput,
            ],
            'containsDOB' => [
                $addressInputContainsDOB,
                $expectedOutputContainsDOB,
            ],
            'containsGender' => [
                $addressInputContainsGender,
                $expectedOutputContainsGender,
            ],
            'someMissingValues' => [
                $addressInputContainsSomeMissingValues,
                $expectedSomeMissingValues,
            ]
        ];
    }
}
