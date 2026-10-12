<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Logging;

use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Support\BindsToStack;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the logging capability.
 *
 *   $stack->add(new LoggingBrick())
 *       ->builder()
 *       ->toRotatingFile(...)
 *       ->withMasking();
 */
final class LoggingBrick implements BindableBrickInterface
{
    use BindsToStack;

    public const string NAME = 'logger-monolog';

    private readonly LoggingOptions $options;

    public function __construct()
    {
        $this->options = new LoggingOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): LoggingBuilder
    {
        return new LoggingBuilder($this->stack(), $this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new LoggingStackProvider($this->options)];
    }
}
