<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

final class ViewOptions
{
    /** @var array<string, string> namespace => absolute filesystem path */
    private array $paths = [];

    /** @var list<class-string> */
    private array $extensions = [];

    /** @var array<string, string> */
    private array $routeNamespace = [];

    private bool $engineAttached = false;

    /**
     * @return array<string, string>
     */
    public function paths(): array
    {
        return $this->paths;
    }

    /**
     * @param array<string, string> $paths namespace => absolute filesystem path
     */
    public function setPaths(array $paths): void
    {
        $this->paths = $paths;
    }

    /**
     * @return list<class-string>
     */
    public function extensions(): array
    {
        return $this->extensions;
    }

    /**
     * @param list<class-string> $extensions
     */
    public function setExtensions(array $extensions): void
    {
        $this->extensions = $extensions;
    }

    /**
     * @return array<string, string>
     */
    public function routeNamespaces(): array
    {
        return $this->routeNamespace;
    }

    /**
     * @param array<string, string> $routeNamespaces
     */
    public function setRouteNamespaces(array $routeNamespaces): void
    {
        $this->routeNamespace = $routeNamespaces;
    }

    public function hasEngine(): bool
    {
        return $this->engineAttached;
    }

    public function markEngineAttached(): void
    {
        $this->engineAttached = true;
    }

    public function checkOptions(): void
    {
        if (!$this->engineAttached) {
            throw InvalidCapabilityOptionsException::missingOption(ViewBrick::NAME, 'withEngine()');
        }
    }
}
