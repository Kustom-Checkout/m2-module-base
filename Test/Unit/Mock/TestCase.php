<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Mock;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @internal
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockFactory
     */
    protected ?MockFactory $mockFactory = null;
    /**
     * @var TestObjectFactory
     */
    protected TestObjectFactory $objectFactory;
    /**
     * @var array
     */
    protected array $dependencyMocks;

    protected function createSingleMock(string $class, array $onlyMethods = [], array $addMethods = [])
    {
        if ($this->mockFactory === null) {
            $this->mockFactory = new MockFactory($this);
        }

        return $this->mockFactory->create($class, $onlyMethods, $addMethods);
    }

    protected function setUpMocks(string $class, array $methodsToMock = [], array $instanceMocks = [])
    {
        parent::setUp();

        if ($this->mockFactory === null) {
            $this->mockFactory = new MockFactory($this);
        }

        $this->objectFactory = new TestObjectFactory('');

        $instance = $this->objectFactory->create($class, $methodsToMock, $instanceMocks);
        $this->dependencyMocks = $this->objectFactory->getDependencyMocks();

        return $instance;
    }
}
