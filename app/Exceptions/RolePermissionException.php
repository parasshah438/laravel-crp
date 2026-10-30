<?php

namespace App\Exceptions;

use Exception;

class RolePermissionException extends Exception
{
    public function __construct($message = "Role permission error", $code = 403, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}