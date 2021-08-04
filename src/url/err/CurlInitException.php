<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\url\err;


use pvc\err\throwable\ErrorExceptionConstants as ec;
use pvc\err\throwable\exception\stock_rebrands\Exception;
use pvc\msg\ErrorExceptionMsg;

class CurlInitException extends Exception
{
    public function __construct()
    {
        $msgText = 'curl_init call failed and returned false instead of a curl handle.';
        $msgVars = [];
        $msg = new ErrorExceptionMsg($msgVars, $msgText);
        $code = ec::CURL_INIT_EXCEPTION;
        $previous = null;
        parent::__construct($msg, $code, $previous);
    }

}