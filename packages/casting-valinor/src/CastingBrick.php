<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Casting;

use Concept\Stack\Contract\BrickInterface;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the casting capability.
 *
 *   $stack->add(new CastingBrick())
 *       ->builder()
 *       ->setCacheDir(...)
 *       ->setDebug($debug);
 */
final class CastingBrick implements BrickInterface
{
    public const string NAME = 'casting-valinor';

    private readonly CastingOptions $options;

    public function __construct()
    {
        $this->options = new CastingOptions();
    }

    public function name(): string
    {
        return self::NAME;
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
}
