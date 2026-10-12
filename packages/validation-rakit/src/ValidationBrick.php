<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Validation;

use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Support\BindsToStack;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the validation capability.
 *
 *   $stack->add(new ValidationBrick())
 *       ->builder()
 *       ->setRules([...])
 *       ->withMasking();
 */
final class ValidationBrick implements BindableBrickInterface
{
    use BindsToStack;

    public const string NAME = 'validation-rakit';

    private readonly ValidationOptions $options;

    public function __construct()
    {
        $this->options = new ValidationOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): ValidationBuilder
    {
        return new ValidationBuilder($this->stack(), $this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new ValidationStackProvider($this->options)];
    }
}
