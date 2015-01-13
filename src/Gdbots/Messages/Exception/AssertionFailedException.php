<?php

namespace Gdbots\Messages\Exception;

use Assert\InvalidArgumentException;

class AssertionFailedException extends InvalidArgumentException implements GdbotsMessagesException {}
