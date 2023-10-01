<?php

namespace iggyvolz\iggyecs\sample\Physics;

use iggyvolz\iggyecs\Event;
use iggyvolz\iggyecs\World;
use UnitEnum;

final readonly class PhysicsFrameEvent extends Event
{
    public function __construct(World $world, UnitEnum|string $entity, public float $deltaTime)
    {
        parent::__construct($world, $entity);
    }
}