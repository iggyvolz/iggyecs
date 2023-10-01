<?php

namespace iggyvolz\iggyecs\sample\Physics;

use iggyvolz\iggyecs\Handler;
use iggyvolz\iggyecs\System;
use iggyvolz\iggyecs\World;
use Revolt\EventLoop;

final class PhysicsSystem extends System
{
    private float $time;

    public function init(World $world): void
    {
        $this->time = microtime(true);
        EventLoop::repeat(0.1, function () use ($world) {
            $deltaTime = ($currentTime = microtime(true)) - $this->time;
            foreach ($world->getEntitiesForComponent(Velocity::class) as $entity) {
                $world->triggerEvent(new PhysicsFrameEvent($world, $entity, $deltaTime));
            }
            $this->time = $currentTime;
        });
    }

    #[Handler]
    public function adjustVelocity(PhysicsFrameEvent $event): void
    {
        $velocity = $event->world->getComponent($event->entity, Velocity::class);
        $position = $event->world->getComponent($event->entity, Position::class);
        if (is_null($position)) return;
        $position->x += $velocity->x * $event->deltaTime;
        $position->y += $velocity->y * $event->deltaTime;
        $position->z += $velocity->z * $event->deltaTime;
    }
}