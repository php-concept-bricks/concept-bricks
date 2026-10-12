<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Events;

use Concept\Stack\Contract\BrickInterface;
use League\Container\ServiceProvider\ServiceProviderInterface;

final class EventBrick implements BrickInterface
{
    public const string NAME = 'event';

    private readonly EventsOptions $options;

    public function __construct()
    {
        $this->options = new EventsOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): EventsBuilder
    {
        return new EventsBuilder($this->options);
    }

    /** @return list<ServiceProviderInterface> */
    public function providers(): array
    {
        return [new EventsStackProvider($this->options)];
    }
}
