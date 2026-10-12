<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Database;

use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Support\BindsToStack;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the database capability.
 *
 *   $stack->add(new DatabaseBrick())
 *       ->builder()
 *       ->setConnection([...])
 *       ->withMasking();
 */
final class DatabaseBrick implements BindableBrickInterface
{
    use BindsToStack;

    public const string NAME = 'database-eloquent';

    private readonly DatabaseOptions $options;

    public function __construct()
    {
        $this->options = new DatabaseOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): DatabaseBuilder
    {
        return new DatabaseBuilder($this->stack(), $this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new DatabaseStackProvider($this->options)];
    }
}
