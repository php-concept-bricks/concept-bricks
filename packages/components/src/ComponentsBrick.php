<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Components;

use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Support\BindsToStack;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the components capability.
 *
 *   $stack->add(new ComponentsBrick())
 *       ->builder()
 *       ->setClasses([...])
 *       ->withDatabase()
 *       ->withHttp();
 */
final class ComponentsBrick implements BindableBrickInterface
{
    use BindsToStack;

    public const string NAME = 'components';

    private readonly ComponentsOptions $options;

    public function __construct()
    {
        $this->options = new ComponentsOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): ComponentsBuilder
    {
        return new ComponentsBuilder($this->stack(), $this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new ComponentsStackProvider($this->options)];
    }
}
