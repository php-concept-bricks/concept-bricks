<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View\Contract;

use Concept\Stack\Contract\BrickInterface;

/**
 * Engine brick plugged into ViewBrick via withEngine().
 * Concrete builder() return type is engine-specific (Twig / Plates / …).
 */
interface ViewEngineBrickInterface extends BrickInterface
{
    public function builder(): object;
}
