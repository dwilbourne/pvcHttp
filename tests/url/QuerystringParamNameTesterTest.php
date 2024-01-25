<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\http\url;

use PHPUnit\Framework\TestCase;
use pvc\http\url\QuerystringParamNameTester;
use pvc\interfaces\regex\RegexInterface;

class QuerystringParamNameTesterTest extends TestCase
{
    protected QuerystringParamNameTester $tester;

    public function setUp(): void
    {
        $regex = $this->createMock(RegexInterface::class);
        $this->tester = new QuerystringParamNameTester($regex);
    }

    /**
     * testConstruct
     * @covers \pvc\http\url\QuerystringParamNameTester::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(QuerystringParamNameTester::class, $this->tester);
    }
}
