<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Responder;

use Klarna\Base\Model\Responder\Result;
use Klarna\Base\Test\Unit\Mock\MockFactory;
use Klarna\Base\Test\Unit\Mock\TestObjectFactory;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Controller\Result\Json;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    private Result $result;
    /**
     * @var array
     */
    private array $dependencyMocks;
    /**
     * @var Json
     */
    private Json $json;

    public function testGetJsonResultReturnsInstance(): void
    {
        $this->dependencyMocks['jsonFactory']->expects($this->once())
            ->method('create')
            ->willReturn($this->json);

        $data = ['a' => 'b'];
        $this->json->expects($this->once())
            ->method('setData')
            ->with($data);

        $httpCode = 200;
        $this->json->expects($this->once())
            ->method('setHttpResponseCode')
            ->with($httpCode);

        static::assertSame($this->json, $this->result->getJsonResult($httpCode, $data));
    }

    protected function setUp(): void
    {
        $mockFactory = new MockFactory($this);
        $objectFactory = new TestObjectFactory('');

        $this->result = $objectFactory->create(Result::class);
        $this->dependencyMocks = $objectFactory->getDependencyMocks();

        $this->json = $mockFactory->create(Json::class);
    }
}
