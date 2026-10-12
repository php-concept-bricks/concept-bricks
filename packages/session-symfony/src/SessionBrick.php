<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Session;

use Concept\Stack\Contract\BrickInterface;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the session capability.
 *
 *   $stack->add(new SessionBrick())
 *       ->builder()
 *       ->setOptions([...])
 *       ->withCsrf();
 */
final class SessionBrick implements BrickInterface
{
    public const string NAME = 'session-symfony';

    private readonly SessionOptions $options;

    public function __construct()
    {
        $this->options = new SessionOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): SessionBuilder
    {
        return new SessionBuilder($this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new SessionStackProvider($this->options)];
    }
}
