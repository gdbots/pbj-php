<?php

namespace Gdbots\Pbj;

abstract class AbstractMixin implements Mixin
{
    /**
     * Mixins cannot be instantiated.
     */
    final private function __construct() {}
}
