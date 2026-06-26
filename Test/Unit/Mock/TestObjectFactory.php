<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Mock;

use Magento\Framework\App\ObjectManager as AppObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Factory to create the test objects that each test class will
 * use to run the tests against. Automatically finds and sets up
 * mocks for all class dependencies.
 *
 * TODO: We should eventually get rid of this abstraction, it doesn't make sense to extend TestCase just for getMockBuilder
 * Increased complexity also in the longer run to maintain tests, when PHPUnit and by extension Magento Testing Framework
 * will change.
 *
 * @internal
 */
class TestObjectFactory extends TestCase
{
    /**
     * @var array
     */
    private $dependencyMocks = [];

    /**
     * Reflects over the given class to find and insert all dependencies
     * into a test object which is returned and used for testing the class.
     *
     * Some mocked dependencies need some or all of their methods defined and/or stubbed.
     * That's where $methodsToMock comes in.
     *
     * @param string $className
     * @param array $methodsToMock
     * @param array $instanceMocks
     * @return object
     */
    public function create(string $className, array $methodsToMock = [], array $instanceMocks = [])
    {
        $mockFactory = new MockFactory($this);

        try {
            $objectManagerHelper = new ObjectManager($this);

            if (method_exists($objectManagerHelper, 'prepareObjectManager')) {
                $objectManagerHelper->prepareObjectManager();
            } else {
                $this->prepareObjectManager();
            }

            $reflection = new \ReflectionClass($className);

            $constructor = $reflection->getConstructor();
            if ($constructor !== null) {
                $params = $constructor->getParameters();

                foreach ($params as $param) {
                    if ($this->isParamObject($param->getType())) {
                        $paramClass = $param->getType()->getName();

                        $paramMockMethods = $this->getParamMockMethods($methodsToMock, $paramClass);
                        $dependencyMock = $mockFactory->create($paramClass, $paramMockMethods);
                        if (isset($instanceMocks[$paramClass])) {
                            $dependencyMock = $instanceMocks[$paramClass];
                        }

                        $this->dependencyMocks[$param->getName()] = $dependencyMock;

                        continue;
                    }
                    $this->handleSpecialConstructorInstanceCases($param, $instanceMocks);
                }
            }

            return $objectManagerHelper->getObject($className, $this->dependencyMocks);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Returns true when the given parameter can be used as a mocked instance
     *
     * @param null|\ReflectionNamedType $type
     * @return bool
     */
    private function isParamObject($type): bool
    {
        return $type instanceof \ReflectionNamedType && !in_array($type->getName(), ['array', 'string']);
    }

    /**
     * Adding special instancs of the constructor (array, ...) to the depency mock attribute
     *
     * @param \ReflectionParameter $param
     * @param array $instanceMocks
     */
    private function handleSpecialConstructorInstanceCases(\ReflectionParameter $param, array $instanceMocks = []): void
    {
        if ($param->getType() === null) {
            $this->dependencyMocks[$param->getName()] = '';
            return;
        }

        $paramClass = $param->getType()->getName();
        if ($paramClass === 'array') {
            $input = [];
            if (isset($instanceMocks[$param->getName()])) {
                $input = $instanceMocks[$param->getName()];
            }
            $this->dependencyMocks[$param->getName()] = $input;
        } elseif ($paramClass === 'string') {
            $this->dependencyMocks[$param->getName()] = '';
        }
    }

    /**
     * Returns all dependency mocks connected to the class given in ::create
     *
     * @return \PHPUnit\Framework\MockObject\MockObject[]
     */
    public function getDependencyMocks(): array
    {
        return $this->dependencyMocks;
    }

    /**
     * Returns an array of all methods that are to be mocked for the given dependency
     *
     * @param array $methodsToMock
     * @param string $paramClass
     * @return array
     */
    private function getParamMockMethods(array $methodsToMock, string $paramClass): array
    {
        if (!isset($methodsToMock[$paramClass])) {
            return [];
        }

        return $methodsToMock[$paramClass];
    }

    /**
     * Helper method to get mock of ObjectManagerInterface
     *
     * @deprecated Support for PHPUnit < 10
     *
     * @param array $map
     */
    public function prepareObjectManager(array $map = [])
    {
        $objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)
            ->addMethods(['getInstance'])
            ->onlyMethods(['get'])
            ->getMockForAbstractClass();

        $objectManagerMock->method('getInstance')->willReturnSelf();
        $objectManagerMock->method('get')->willReturnMap($map);

        $reflectionProperty = new \ReflectionProperty(AppObjectManager::class, '_instance');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($objectManagerMock, $objectManagerMock);
    }
}
