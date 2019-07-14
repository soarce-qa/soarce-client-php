<?php

namespace Soarce\Action;

class Exception extends \Soarce\Exception
{
    public const DATA_DIRECTORY__NOT_WRITEABLE      = 1;
    public const DATA_DIRECTORY__NOT_READABLE       = 2;
    public const NAME_OF_USECASE_MISSING_IN_REQUEST = 3;
    public const TRACEFILE_DIRECTORY_NOT_READABLE   = 4;

}
