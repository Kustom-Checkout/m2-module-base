<?php

/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

declare(strict_types=1);

namespace Klarna\Base\Test\Integration\Observer;

use Klarna\Base\Helper\Debug\DebugDataObject;
use Magento\Framework\Event;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DebugDataCollectorConfigObserverTest extends TestCase
{
    /**
     * @var ObjectManagerInterface|null
     */
    private ?ObjectManagerInterface $objectManager = null;

    /**
     * @var Event\ManagerInterface|null
     */
    private ?Event\ManagerInterface $eventManager = null;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->eventManager = $this->objectManager->create(Event\ManagerInterface::class);
    }

    public function testExecutionAddsAllKlarnaConfigDataToDebugDataObject(): void
    {
        $dataObject = $this->objectManager->create(DebugDataObject::class);

        $this->eventManager->dispatch('klarna_debug_data_collector', [
            'debug_data_object' => $dataObject
        ]);

        $this->assertNotEmpty($dataObject->getData('klarna_configs'));
        $this->assertNotEmpty($dataObject->getData('klarna_tax_configs'));
    }
}
