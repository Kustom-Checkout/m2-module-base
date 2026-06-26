<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Helper;

use Klarna\Base\Helper\GUIDGenerator;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use PHPUnit\Framework\TestCase;

class GUIDGeneratorTest extends TestCase
{
    /**
     * @var GUIDGenerator
     */
    private $guidGenerator;

    /**
     * @var string
     */
    private string $guid;

    /**
     * @var string[]
     */
    private array $splitGUID;

    public function testGenerateGUIDIsStringAndHasFiveParts(): void
    {
        static::assertIsString($this->guid);
        static::assertCount(5, $this->splitGUID);
    }

    public function testGenerateGUIDAssertFirstPartIsValid(): void
    {
        static::assertEquals(8, strlen($this->splitGUID[0]));
    }

    public function testGenerateGUIDAssertSecondPartIsValid(): void
    {
        static::assertEquals(4, strlen($this->splitGUID[1]));
    }

    public function testGenerateGUIDAssertThirdPartIsValid(): void
    {
        static::assertEquals(4, strlen($this->splitGUID[2]));
    }

    public function testGenerateGUIDAssertFourthPartIsValid(): void
    {
        static::assertEquals(4, strlen($this->splitGUID[3]));
    }

    public function testGenerateGUIDAssertFifthPartIsValid(): void
    {
        static::assertEquals(12, strlen($this->splitGUID[4]));
    }

    protected function setUp(): void
    {
        $objectFactory = new TestObjectFactory('');

        $this->guidGenerator = $objectFactory->create(GUIDGenerator::class);
        $this->guid = $this->guidGenerator->generateGUID();
        $this->splitGUID = explode('-', $this->guid);
    }
}
