<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Console;

use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Support\BindsToStack;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the console capability.
 *
 *   $stack->add(new ConsoleBrick())
 *       ->builder()
 *       ->setName(...)
 *       ->setCommands([...])
 *       ->withLogging();
 */
final class ConsoleBrick implements BindableBrickInterface
{
    use BindsToStack;

    public const string NAME = 'console-symfony';

    private readonly ConsoleOptions $options;

    public function __construct()
    {
        $this->options = new ConsoleOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): ConsoleBuilder
    {
        return new ConsoleBuilder($this->stack(), $this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new ConsoleStackProvider($this->options)];
    }
}
