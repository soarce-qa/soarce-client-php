<?php

namespace Soarce\Action;

class Exception extends \Soarce\Exception
{
    const DATA_DIRECTORY__NOT_WRITEABLE      = 1;
    const DATA_DIRECTORY__NOT_READABLE       = 2;
    const NAME_OF_USECASE_MISSING_IN_REQUEST = 3;
    const MISSING_FILENAME_PARAMETER         = 4;
    const FILE_NOT_FOUND                     = 5;
}
