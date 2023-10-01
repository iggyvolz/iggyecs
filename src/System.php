<?php

namespace iggyvolz\iggyecs;

use Iggyvolz\SimpleAttributeReflection\AttributeReflection;

abstract class System
{
    public function init(World $world): void
    {

    }

    /** @internal */
    public final function getHandlers(): \Generator
    {
        foreach ((new \ReflectionClass(static::class))->getMethods() as $method) {
            if (AttributeReflection::getAttribute($method, Handler::class)) {
                $parameters = $method->getParameters();
                if (count($parameters) !== 1) throw new \LogicException("Invalid handler!");
                $eventParameterType = $parameters[0]->getType();
                if ($eventParameterType instanceof \ReflectionNamedType) {
                    yield $eventParameterType->getName() => $method->getClosure($this);
                } else if ($eventParameterType instanceof \ReflectionUnionType) {
                    foreach ($eventParameterType->getTypes() as $subtype) {
                        if ($subtype instanceof \ReflectionNamedType) {
                            yield $subtype->getName() => $method->getClosure($this);
                        } else {
                            throw new \LogicException("Invalid handler!");
                        }
                    }
                } else {
                    throw new \LogicException("Invalid handler!");
                }
            }
        }
    }
}