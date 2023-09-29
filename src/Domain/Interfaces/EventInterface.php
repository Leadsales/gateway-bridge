<?php

namespace Gateway\Domain\Interfaces;

interface EventInterface
{
    public function publish();
    
    public function emmit();
}
