<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\url;

/**
 * encapsulate the idea of a querystring.  parse_str mangles parameter names so they conform to the php variable
 * naming convention, but a different parser might not and this encapsulation can provide for that. Feed the params
 * attribute to http_build_query to get an encoded querystring.
 */
class QueryString
{
    protected array $params = [];

    public function setParams(array $params) : void
    {
        $this->params = $params;
    }

    public function getParams() : array
    {
        return $this->params;
    }

}