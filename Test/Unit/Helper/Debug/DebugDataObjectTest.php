<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Helper\Debug;

use Klarna\Base\Helper\Debug\DebugDataObject;
use PHPUnit\Framework\Attributes\DataProvider;
use Klarna\Base\Test\Unit\Mock\TestCase;

/**
 * @coversDefaultClass \Klarna\Base\Helper\Debug\DebugDataObject
 */
class DebugDataObjectTest extends TestCase
{
    /**
     * @var object
     */
    private object $debugDataObject;

    #[DataProvider('dataProviderForTestCanSetAndRetrieveTheData')]
    /**
     * @dataProvider dataProviderForTestCanSetAndRetrieveTheData
     */
    public function testCanSetAndRetrieveTheData(array $modules, array $modulesToIgnore, array $expected): void
    {
        foreach ($modules as $module) {
            $this->debugDataObject->addData($module[0], $module[1]);
        }
        $this->debugDataObject->ignoreModules($modulesToIgnore);

        static::assertSame($expected, $this->debugDataObject->getData());
    }

    public static function dataProviderForTestCanSetAndRetrieveTheData(): array
    {
        return [
            [
                'modules' => [
                    ['module', 'data'],
                ],
                'modulesToIgnore' => [],
                'expected' => [
                    'module' => 'data',
                ]
            ],
            [
                'modules' => [
                    ['module', 'data'],
                    ['module2', 'data2']
                ],
                'modulesToIgnore' => [],
                'expected' => [
                    'module' => 'data',
                    'module2' => 'data2',
                ]
            ],
            [
                'modules' => [
                    ['module', 'data'],
                    ['module2', 'data2']
                ],
                'modulesToIgnore' => ['module2'],
                'expected' => [
                    'module' => 'data',
                ]
            ],
            [
                'modules' => [
                    ['module', 'data'],
                    ['module2', 'data2']
                ],
                'modulesToIgnore' => ['module', 'module2'],
                'expected' => []
            ],
            [
                'modules' => [
                    ['module', 'data'],
                    ['module2', 'data2']
                ],
                'modulesToIgnore' => ['moduleThatDoesNotExist'],
                'expected' => [
                    'module' => 'data',
                    'module2' => 'data2',
                ]
            ],
        ];
    }

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->debugDataObject = parent::setUpMocks(DebugDataObject::class);
    }
}
