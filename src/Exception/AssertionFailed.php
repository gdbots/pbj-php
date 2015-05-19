<?php

namespace Gdbots\Pbj\Exception;

use Assert\InvalidArgumentException;

class AssertionFailed extends InvalidArgumentException implements GdbotsPbjException {}
