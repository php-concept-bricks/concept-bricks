<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Casting;

use Concept\Stack\Capability\Capability;
use Concept\Stack\Contract\BrickInterface;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the casting capability (no stack bind — no with* deps).
 *
 *   $stack->add(new CastingBrick())
 *       ->builder()
 *       ->setCacheDir(...)
 *       ->setDebug($debug);
 */
final class CastingBrick implements BrickInterface
{
    private readonly CastingOptions $options;

    public function __construct()
    {
        $this->options = new CastingOptions();
    }

    public function name(): string
    {
        return Capability::CASTING;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): CastingBuilder
    {
        return new CastingBuilder($this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new CastingStackProvider($this->options)];
    }

    public function checkOptions(): void
    {
        // CastingOptions has no required fields today.
    }
}
