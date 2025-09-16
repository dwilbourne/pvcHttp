<?php

namespace pvcTests\http\url;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\filtervar\FilterVarValidateUrl;
use pvc\http\url\ValTesterUrl;

class ValTesterUrlTest extends TestCase
{
    protected \PHPUnit\Framework\MockObject\MockObject $filterVarValidateUrlMock;
    protected ValTesterUrl $urlTester;

    public function setUp(): void
    {
        $this->filterVarValidateUrlMock = $this->createMock(FilterVarValidateUrl::class);
        $this->urlTester = new ValTesterUrl($this->filterVarValidateUrlMock);
    }

    /**
     * @return void
     * @covers \pvc\http\url\ValTesterUrl::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ValTesterUrl::class, $this->urlTester);
    }
}
