<?php

namespace iggyvolz\iggyecs\sample\Debug;

use iggyvolz\iggyecs\Handler;
use iggyvolz\iggyecs\sample\Physics\Position;
use iggyvolz\iggyecs\System;
use iggyvolz\iggyecs\World;
use Revolt\EventLoop;
use UnitEnum;

final class DebugSystem extends System
{
    private array $words = [];

    public function init(World $world): void
    {
        $this->words = array_map(trim(...), file(__DIR__ . "/english.txt"));
        EventLoop::repeat(0.05, function () use ($world) {
            foreach ($world->getEntitiesForComponent(Position::class) as $entity) {
                $world->triggerEvent(new DebugEvent($world, $entity));
            }
        });
    }

    private function prettify(string|UnitEnum $entity, int $length = 3): string
    {
        if ($entity instanceof UnitEnum) {
            return $entity::class . "::" . $entity->name;
        } else {
            $parts = [];
            for ($i = 0; $i < $length; $i++) {
                $byte = $entity[strlen($entity) - $i - 1];
                $parts[] = $this->words[ord($byte) * 8];
            }
            return implode("-", $parts);
        }
    }

    #[Handler]
    public function debug(DebugEvent $e): void
    {
        echo "Entity " . $this->prettify($e->entity) . " is at position " . $e->world->getComponent($e->entity, Position::class) . PHP_EOL;
    }
}