<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Http;

use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Support\BindsToStack;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the HTTP capability.
 *
 * Builder / Options / StackProvider stay as the three implementation files.
 * Options are checked inside HttpStackProvider — not exposed to the app.
 *
 *   $stack->add(new HttpBrick())
 *       ->builder()
 *       ->setRoutes([...])
 *       ->withFormRequests();
 */
final class HttpBrick implements BindableBrickInterface
{
    use BindsToStack;

    public const string NAME = 'http';

    private readonly HttpOptions $options;

    public function __construct()
    {
        $this->options = new HttpOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    /**
     * Fluent configuration (same HttpBuilder as before).
     * Call only after StackBuilder::add() (bind).
     */
    public function builder(): HttpBuilder
    {
        return new HttpBuilder($this->stack(), $this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new HttpStackProvider($this->options)];
    }
}
