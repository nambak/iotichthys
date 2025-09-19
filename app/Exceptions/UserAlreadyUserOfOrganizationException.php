<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class UserAlreadyUserOfOrganizationException extends Exception
{
    public function __construct(string $message = '사용자가 이미 멤버입니다.', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
