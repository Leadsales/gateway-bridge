<?php

namespace Gateway\Domain\Abstracts;

use Gateway\Domain\Interfaces\CommunicatorInterface;

abstract class Sender
{
    protected CommunicatorInterface $communicator;
    protected string $protocol;

    public function __construct(CommunicatorInterface $communicator)
    {
        $this->communicator = $communicator;
    }
}