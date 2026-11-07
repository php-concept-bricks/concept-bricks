<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ViewTwig;

final class ViewTwigBuilder
{
    public function __construct(
        private readonly ViewTwigOptions $options,
    ) {}

    /**
     * Absolute path to the primary Twig templates root (FilesystemLoader).
     */
    public function setViewsPath(string $viewsPath): self
    {
        $this->options->setViewsPath($viewsPath);

        return $this;
    }

    /**
     * Absolute Twig cache directory. Omit (null default) → no filesystem cache.
     */
    public function setCacheDir(string $cacheDir): self
    {
        $this->options->setCacheDir($cacheDir);

        return $this;
    }

    public function setDebug(bool $debug = true): self
    {
        $this->options->setDebug($debug);

        return $this;
    }

    /**
     * File suffix appended when the template name has none (default `.twig`).
     */
    public function setDefaultExtension(string $defaultExtension): self
    {
        $this->options->setDefaultExtension($defaultExtension);

        return $this;
    }
}
