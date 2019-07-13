<?php

namespace Soarce;

use RuntimeException;

class Exception extends RuntimeException
{
    /* general exceptions */

    /* FRONT_CONTROLLER exceptions */
    public const FRONTCONTROLLER__NO_ACTION_PARAMETER_NAME = 1000;
    public const FRONTCONTROLLER__NO_ACTION_NAME = 1001;
}
