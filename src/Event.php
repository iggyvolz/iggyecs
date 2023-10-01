<?php

namespace iggyvolz\iggyecs;

use UnitEnum;

abstract readonly class Event
{
    public function __construct(
        public World           $world,
        public string|UnitEnum $entity
    )
    {
    }
}