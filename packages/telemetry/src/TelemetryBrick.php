<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Telemetry;

use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Support\BindsToStack;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the telemetry capability.
 *
 *   $stack->add(new TelemetryBrick())
 *       ->builder()
 *       ->setEnabled(true)
 *       ->setLogs(true)
 *       ->setEventName(...);
 */
final class TelemetryBrick implements BindableBrickInterface
{
    use BindsToStack;

    public const string NAME = 'telemetry';

    private readonly TelemetryOptions $options;

    public function __construct()
    {
        $this->options = new TelemetryOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): TelemetryBuilder
    {
        return new TelemetryBuilder($this->stack(), $this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new TelemetryStackProvider($this->options)];
    }
}
