<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ViewPlates;

use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

final class ViewPlatesOptions
{
    private string $viewsPath = '';

    private ?string $defaultExtension = null;

    public function viewsPath(): string
    {
        return $this->viewsPath;
    }

    public function setViewsPath(string $viewsPath): void
    {
        $this->viewsPath = $viewsPath;
    }

    public function defaultExtension(): ?string
    {
        return $this->defaultExtension;
    }

    public function setDefaultExtension(string $defaultExtension): void
    {
        $this->defaultExtension = $defaultExtension;
    }

    public function checkOptions(): void
    {
        if ($this->viewsPath === '') {
            throw InvalidCapabilityOptionsException::missingOption(
                ViewPlatesBrick::NAME,
                'setViewsPath()',
            );
        }
    }
}
