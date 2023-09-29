<?php

namespace Leadsales\GatewayBridge\Domain\Abstracts;

use Leadsales\GatewayBridge\Domain\Interfaces\GatewayInterface;

abstract class HandlerGateway
{
    protected GatewayInterface $communicator;
    protected string $protocol;

    public function __construct(GatewayInterface $communicator)
    {
        $this->communicator = $communicator;
    }
}