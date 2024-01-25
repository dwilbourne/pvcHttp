<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\http\url;

use pvc\interfaces\regex\RegexInterface;
use pvc\validator\val_tester\regex\RegexTester;

/**
 * Class QuerystringParamNameTester
 *
 *  This class restricts parameter names so that they ought to work on just about any server.  Although there is no
 *  standard for querystring parameter names as far as I know, we make sure that the parameter names are formed
 *  such that they could be php variable names, and then we ought to be safe.  php variable names must begin with a
 *  letter, can contain letters, numbers and underscores....
 */
class QuerystringParamNameTester extends RegexTester
{
    /**
     * @param RegexInterface $regexPhpVariableName
     */
    public function __construct(RegexInterface $regexPhpVariableName)
    {
        $this->setRegex($regexPhpVariableName);
    }
}
