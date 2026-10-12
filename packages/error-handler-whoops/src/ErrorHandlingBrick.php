<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling;

use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Support\BindsToStack;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the error-handling capability.
 *
 *   $stack->add(new ErrorHandlingBrick())
 *       ->builder()
 *       ->setDebug($debug, new PrettyPageHandler())
 *       ->setLogReporting()
 *       ->withViewRenderer(...);
 */
final class ErrorHandlingBrick implements BindableBrickInterface
{
    use BindsToStack;

    public const string NAME = 'error-handler-whoops';

    private readonly ErrorHandlingOptions $options;

    public function __construct()
    {
        $this->options = new ErrorHandlingOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): ErrorHandlingBuilder
    {
        return new ErrorHandlingBuilder($this->stack(), $this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new ErrorHandlingStackProvider($this->options)];
    }
}
