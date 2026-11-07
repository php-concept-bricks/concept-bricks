<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ViewTwig;

use Concept\Stack\Bricks\View\Contract\ViewEngineBrickInterface;
use Concept\Stack\Bricks\View\ViewBrick;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Twig engine for ViewBrick::withEngine().
 *
 *   $stack->add(new ViewBrick())
 *       ->builder()
 *       ->withEngine(new ViewTwigBrick())
 *       ->setViewsPath(...);
 *
 * @implements ViewEngineBrickInterface<ViewTwigBuilder>
 */
final class ViewTwigBrick implements ViewEngineBrickInterface
{
    public const string NAME = 'view-twig';

    private readonly ViewTwigOptions $options;

    public function __construct()
    {
        $this->options = new ViewTwigOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [ViewBrick::NAME];
    }

    public function builder(): ViewTwigBuilder
    {
        return new ViewTwigBuilder($this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new ViewTwigStackProvider($this->options)];
    }
}
