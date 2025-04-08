<?php

namespace pvc\http\url;

use pvc\filtervar\FilterVarValidateUrl;
use pvc\validator\val_tester\filter_var\FilterVarTester;

class ValTesterUrl extends FilterVarTester
{
    public function __construct(FilterVarValidateUrl $filterVarValidateUrl)
    {
        parent::__construct($filterVarValidateUrl);
    }
}