<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Stack\Bricks\Http\HttpBrick;
use Concept\Stack\Contract\BindableBrickInterface;
use Concept\Stack\Support\BindsToStack;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * View registry capability (paths / extensions / response factory).
 * Configure the engine brick first, then attach it via withEngine():
 *
 *   $twig = new ViewTwigBrick();
 *   $twig->builder()->setViewsPath(...);
 *
 *   $stack->add(new ViewBrick())
 *       ->builder()
 *       ->setPaths([...])
 *       ->withEngine($twig);
 */
final class ViewBrick implements BindableBrickInterface
{
    use BindsToStack;

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
        return new ViewBuilder($this->stack(), $this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new ViewStackProvider($this->options)];
    }
}
