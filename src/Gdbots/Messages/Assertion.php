<?php

namespace Gdbots\Messages;

use Assert\Assertion as BaseAssertion;

class Assertion extends BaseAssertion
{
    protected static $exceptionClass = 'Gdbots\Messages\Exception\AssertionFailedException';
}
