<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\url\err;

use pvc\msg\ErrorExceptionMsg;
use pvc\err\throwable\exception\stock_rebrands\Exception;
use pvc\err\throwable\ErrorExceptionConstants as ec;
use Throwable;

/**
 * Class InvalidUrlSchemeException
 */
class InvalidUrlSchemeException extends Exception
{
    public function __construct(string $scheme, Throwable $previous = null)
    {
        $vars = array($scheme);
        $msgText = 'Invalid Url scheme in url.  Scheme = %s';
        $msg = new ErrorExceptionMsg($vars, $msgText);
        $code = ec::INVALID_URL_SCHEME_EXCEPTION;
        $previous = null;
        parent::__construct($msg, $code, $previous);
    }
}
