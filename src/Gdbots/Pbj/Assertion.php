<?php

namespace Gdbots\Pbj;

use Assert\Assertion as BaseAssertion;

class Assertion extends BaseAssertion
{
    protected static $exceptionClass = 'Gdbots\Pbj\Exception\AssertionFailedException';
}
