<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ViewTwig;

use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

final class ViewTwigOptions
{
    private string $viewsPath = '';

    private ?string $cacheDir = null;

    private bool $debug = false;

    private ?string $defaultExtension = null;

    public function viewsPath(): string
    {
        return $this->viewsPath;
    }

    public function setViewsPath(string $viewsPath): void
    {
        $this->viewsPath = $viewsPath;
    }

    public function cacheDir(): ?string
    {
        return $this->cacheDir;
    }

    public function setCacheDir(?string $cacheDir): void
    {
        $this->cacheDir = $cacheDir;
    }

    public function debug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
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
                ViewTwigBrick::NAME,
                'setViewsPath()',
            );
        }
    }
}
