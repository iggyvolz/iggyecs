<?php

namespace iggyvolz\iggyecs\sample;

use iggyvolz\iggyecs\sample\Debug\DebugSystem;
use iggyvolz\iggyecs\sample\Physics\PhysicsSystem;
use iggyvolz\iggyecs\sample\Physics\Position;
use iggyvolz\iggyecs\sample\Physics\Velocity;
use iggyvolz\iggyecs\World;
use Revolt\EventLoop;

require_once __DIR__ . "/../vendor/autoload.php";

$world = new World();
$myGameObject = $world->createEntity();
$world->registerSystem(new PhysicsSystem());
$world->registerSystem(new DebugSystem());
$world->addComponent($myGameObject, Position::class);
$world->addComponent($myGameObject, Velocity::class, ["x" => 1, "y" => 2]);

$world->addComponent(StaticEntities::MyAmazingEntity, Position::class, ["x" => -100]);
$world->addComponent(StaticEntities::MyAmazingEntity, Velocity::class, ["y" => -1]);
EventLoop::run();
