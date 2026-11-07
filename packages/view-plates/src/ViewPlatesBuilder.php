<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ViewPlates;

final class ViewPlatesBuilder
{
    public function __construct(
        private readonly ViewPlatesOptions $options,
    ) {}

    /**
     * Absolute path to the primary Plates templates root.
     */
    public function setViewsPath(string $viewsPath): self
    {
        $this->options->setViewsPath($viewsPath);

        return $this;
    }

    /**
     * File suffix for Plates templates (default `.php`).
     */
    public function setDefaultExtension(string $defaultExtension): self
    {
        $this->options->setDefaultExtension($defaultExtension);

        return $this;
    }
}
