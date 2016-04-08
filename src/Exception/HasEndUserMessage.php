<?php

namespace Gdbots\Pbj\Exception;

interface HasEndUserMessage extends GdbotsPbjException
{
    /**
     * Returns a human friendly exception message that:
     *
     * - MUST NOT leak any environment data, credentials, sessions etc.
     * - SHOULD be descriptive and HELPFUL.
     *
     * @return string
     */
    public function getEndUserMessage();

    /**
     * If available, returns a url to a webpage with more details
     * on the exception that has occurred or how to get help.
     *
     * @return string
     */
    public function getEndUserHelpLink();
}
