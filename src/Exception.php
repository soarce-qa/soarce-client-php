<?php

namespace Soarce;

use RuntimeException;

class Exception extends RuntimeException
{
    /* general exceptions */

    /* FRONT_CONTROLLER exceptions */
    const FRONTCONTROLLER__NO_ACTION_PARAMETER_NAME = 1000;
    const FRONTCONTROLLER__NO_ACTION_NAME = 1001;
}
