<?php

namespace pvc\http\psr7;

use GuzzleHttp\Psr7\Query;
use pvc\interfaces\http\psr7\QueryStringInterface;

class QueryString implements QueryStringInterface
{

    public static function parse(
        string $str,
        bool|int $urlEncoding = true
    ): array {
        return Query::parse($str, $urlEncoding);
    }

    public static function build(
        array $params,
        false|int $encoding = PHP_QUERY_RFC3986,
        bool $treatBoolsAsInts = true
    ): string {
        return Query::build($params, $encoding, $treatBoolsAsInts);
    }
}