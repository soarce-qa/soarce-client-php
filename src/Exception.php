<?php

namespace Soarce;

use RuntimeException;

class Exception extends RuntimeException
{
    /* general exceptions */

    /* ACTION exceptions */
    public const ACTION__NO_ACTION_PARAMETER_NAME = 1000;
    public const ACTION__NO_ACTION_NAME = 1001;
}
