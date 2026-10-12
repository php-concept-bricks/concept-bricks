<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Stack\Bricks\Http\HttpBrick;
use Concept\Stack\Contract\BrickInterface;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the view capability.
 *
 *   $stack->add(new ViewBrick())
 *       ->builder()
 *       ->setPaths([...])
 *       ->withTwig()
 *       ->setViewsPath(...);
 */
final class ViewBrick implements BrickInterface
{
    public const string NAME = 'view';

    private readonly ViewOptions $options;

    public function __construct()
    {
        $this->options = new ViewOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [HttpBrick::NAME];
    }

    public function builder(): ViewBuilder
    {
        return new ViewBuilder($this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new ViewStackProvider($this->options)];
    }
}
