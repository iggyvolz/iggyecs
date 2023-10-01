<?php

namespace iggyvolz\iggyecs;

use Attribute;

/**
 * Marks a method as a handler
 * Method must have only one parameter for the event type:
 * - string|UnitEnum $entity
 * - (event type) $event
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class Handler
{

}