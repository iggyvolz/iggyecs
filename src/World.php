<?php

namespace iggyvolz\iggyecs;

use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use UnitEnum;

class World
{
    private TreeMapper $treeMapper;

    public function __construct(
        private int $defaultEntityLength = 16,
    )
    {
        $this->treeMapper = (new MapperBuilder())->mapper();
    }

    /**
     * @var array<string,array<class-string>,object>>
     */
    private array $componentsByEntity = [];

    /**
     * @var array<class-string,array<string,object>>
     */
    private array $entitiesByComponent = [];

    /** @var array<string,UnitEnum> */
    private array $normalizedEntities = [];

    private function normalizeEntity(UnitEnum|string $entity): string
    {
        if (is_string($entity)) {
            return $entity;
        } elseif (($key = array_search($entity, $this->normalizedEntities)) !== false) {
            return $key;
        } else {
            $this->normalizedEntities[$key = $this->_createEntity(EntityType::FromEnum)] = $entity;
            return $key;
        }
    }

    private function denormalizeEntity(string $entity): string|UnitEnum
    {
        return $entity[0] === 'i' ? $entity : $this->normalizedEntities[$entity];
    }

    public function createEntity(): string
    {
        return $this->_createEntity(EntityType::Instantiated);
    }

    /**
     * Helper function for removing all components from an entity
     */
    public function destroyEntity(string|UnitEnum $entity): void
    {
        foreach ($this->getComponents($entity) as $component) {
            $this->removeEntityComponent($entity, $component::class);
        }
    }

    private function _createEntity(EntityType $entityType): string
    {
        return $entityType->value . random_bytes($this->defaultEntityLength);
    }

    /**
     * @template T of object
     * @param class-string<T> $component
     * @return T
     */
    public function addComponent(string|UnitEnum $entity, string $component, array $data = []): object
    {
        $component = $this->treeMapper->map($component, $data);
        $nEntity = self::normalizeEntity($entity);
        $this->componentsByEntity[$nEntity] ??= [];
        $this->componentsByEntity[$nEntity][$component::class] = $component;
        $this->entitiesByComponent[$component::class] ??= [];
        $this->entitiesByComponent[$component::class][$nEntity] = $component;
        return $component;
    }

    /**
     * @template T of object
     * @param class-string<T> $component
     * @return ?T
     */
    public function getComponent(string|UnitEnum $entity, string $component): ?object
    {
        return $this->getComponents($entity)[$component] ?? null;
    }

    /**
     * @param string|UnitEnum $entity
     * @return array<string,object>
     */
    public function getComponents(string|UnitEnum $entity): array
    {
        return $this->componentsByEntity[self::normalizeEntity($entity)] ?? [];
    }

    /**
     * @return list<string|UnitEnum>
     */
    public function getEntitiesForComponent(string $component): array
    {
        return array_map($this->denormalizeEntity(...), array_keys($this->entitiesByComponent[$component]));
    }

    /**
     * @return list<string|UnitEnum>
     */
    public function getAllComponents(string $component): array
    {
        return $this->entitiesByComponent[$component] ?? [];
    }

    public function removeEntityComponent(string|UnitEnum $entity, string $component): void
    {
        $nEntity = self::normalizeEntity($entity);
        $this->componentsByEntity[$nEntity] ??= [];
        unset($this->componentsByEntity[$nEntity][$component]);
        $this->entitiesByComponent[$component] ??= [];
        unset($this->entitiesByComponent[$component][$nEntity]);
    }

    /** @var array<class-string<Event>, list<Closure(Event): void> */
    private array $eventHandlers = [];

    public function triggerEvent(Event $event): void
    {
        foreach (($this->eventHandlers[$event::class] ?? []) as $handler) {
            $handler($event);
        }
    }

    public function registerSystem(System $system): void
    {
        $system->init($this);
        foreach ($system->getHandlers() as $eventName => $handler) {
            $this->eventHandlers[$eventName] ??= [];
            $this->eventHandlers[$eventName][] = $handler;
        }
    }
}