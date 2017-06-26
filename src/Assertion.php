<?php

namespace Gdbots\Pbj;

use Assert\Assertion as BaseAssertion;
use Gdbots\Pbj\Exception\AssertionFailed;

class Assertion extends BaseAssertion
{
    protected static $exceptionClass = AssertionFailed::class;
}
