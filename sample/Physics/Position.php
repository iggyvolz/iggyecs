<?php

namespace iggyvolz\iggyecs\sample\Physics;

use iggyvolz\iggyecs\Component;

#[Component]
final class Position
{
    public function __construct(
        public float $x = 0,
        public float $y = 0,
        public float $z = 0,
    )
    {
    }

    public function __toString(): string
    {
        return "($this->x, $this->y, $this->z)";
    }
}