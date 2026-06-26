<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Api;

use Klarna\Base\Model\Api\MagentoToKlarnaLocaleMapper;
use Klarna\Base\Test\Unit\Mock\MockFactory;
use Magento\Framework\Locale\ResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Magento\Store\Model\Store;

class MagentoToKlarnaLocaleMapperTest extends TestCase
{
    /**
     * @var ResolverInterface
     */
    private ResolverInterface $mockedLocaleResolver;
    /**
     * @var Store
     */
    private Store $store;

    #[DataProvider('localeDataProvider')]
    /**
     * @dataProvider localeDataProvider
     *
     * @param string $input
     * @param string $expectedOutput
     */
    public function testGetMappedLocaleFromBCP47ToRFC1766(string $input, string $expectedOutput): void
    {
        $this->mockedLocaleResolver->method('getLocale')->willReturn($input);

        $mapper = new MagentoToKlarnaLocaleMapper($this->mockedLocaleResolver);
        $actualOutput = $mapper->getLocale($this->store);

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    /**
     * Provide different locales
     *
     * @return array
     */
    public static function localeDataProvider(): array
    {
        return [
            // should map nb_no to no-NO since Klarna API still using RFC1766
            ['nb_no', 'nb-NO'],
            // everything else should work as before
            ['en_US', 'en-US'],
            ['en_GB', 'en-GB'],
            ['fr_FR', 'fr-FR'],
            ['de_DE', 'de-DE'],
        ];
    }

    protected function setUp(): void
    {
        $this->mockedLocaleResolver = $this->createMock(ResolverInterface::class);

        $mockFactory = new MockFactory($this);
        $this->store = $mockFactory->create(Store::class);
    }
}
