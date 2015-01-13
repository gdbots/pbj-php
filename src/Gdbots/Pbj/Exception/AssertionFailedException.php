<?php

namespace Gdbots\Pbj\Exception;

use Assert\InvalidArgumentException;

class AssertionFailedException extends InvalidArgumentException implements GdbotsPbjException {}
