<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\url;

use PHPUnit\Framework\TestCase;
use pvc\url\QueryString;

class QueryStringTest extends TestCase
{
    protected QueryString $qstrObject;

    public function setUp(): void
    {
        $this->qstrObject = new QueryString();
    }

    public function testSetGetParams() : void
    {
        $array = [1, 2, 3, 4];
        $this->qstrObject->setParams($array);
        self::assertEquals($array, $this->qstrObject->getParams());
    }
}
