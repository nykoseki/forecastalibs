<?php

namespace Forecasta\Loader\Xml;

use \Throwable;

class XMLLoaderException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }


}