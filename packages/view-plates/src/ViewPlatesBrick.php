<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ViewPlates;

use Concept\Stack\Bricks\View\Contract\ViewEngineBrickInterface;
use Concept\Stack\Bricks\View\ViewBrick;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Plates engine for ViewBrick::withEngine().
 *
 *   $plates = new ViewPlatesBrick();
 *   $plates->builder()->setViewsPath(...);
 *
 *   $stack->add(new ViewBrick())->builder()->withEngine($plates);
 */
final class ViewPlatesBrick implements ViewEngineBrickInterface
{
    public const string NAME = 'view-plates';

    private readonly ViewPlatesOptions $options;

    public function __construct()
    {
        $this->options = new ViewPlatesOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [ViewBrick::NAME];
    }

    public function builder(): ViewPlatesBuilder
    {
        return new ViewPlatesBuilder($this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new ViewPlatesStackProvider($this->options)];
    }
}
