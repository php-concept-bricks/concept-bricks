<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View\Contract;

use Concept\Stack\Contract\BrickInterface;

/**
 * Engine brick for ViewBuilder::withEngine().
 *
 * Configure via the concrete brick's builder(), then attach the brick:
 *
 *   $twig = new ViewTwigBrick();
 *   $twig->builder()->setViewsPath(...);
 *
 *   $stack->add(new ViewBrick())->builder()->withEngine($twig);
 */
interface ViewEngineBrickInterface extends BrickInterface
{
}
