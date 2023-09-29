<?php

namespace Gateway\Domain\Abstracts;

use Gateway\Domain\Interfaces\GatewayInterface;

abstract class HandlerGateway
{
    protected GatewayInterface $communicator;
    protected string $protocol;

    public function __construct(GatewayInterface $communicator)
    {
        $this->communicator = $communicator;
    }
}