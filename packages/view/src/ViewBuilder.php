<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Stack\Bricks\View\Contract\ViewEngineBrickInterface;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;

final class ViewBuilder
{
    private const string ERR_ENGINE_XOR = 'Capability "view" accepts only one engine via withEngine().';

    public function __construct(
        private readonly StackBuilder $parent,
        private readonly ViewOptions $options,
    ) {}

    /**
     * Named template paths (namespace => absolute filesystem path).
     *
     * @param array<string, string> $paths
     */
    public function setPaths(array $paths): self
    {
        $this->options->setPaths($paths);

        return $this;
    }

    /**
     * Engine extension class names (resolved from the container).
     *
     * @param list<class-string> $extensions
     */
    public function setExtensions(array $extensions): self
    {
        $this->options->setExtensions($extensions);

        return $this;
    }

    /**
     * @param array<string, string> $routeNamespaces
     */
    public function setRouteNamespaces(array $routeNamespaces): self
    {
        $this->options->setRouteNamespaces($routeNamespaces);

        return $this;
    }

    /**
     * Attach a configured view engine brick (Twig, Plates, …). XOR — one engine per stack.
     *
     * @throws InvalidCapabilityOptionsException when an engine is already attached
     */
    public function withEngine(ViewEngineBrickInterface $engine): self
    {
        if ($this->options->hasEngine()) {
            throw new InvalidCapabilityOptionsException(self::ERR_ENGINE_XOR);
        }

        $this->options->markEngineAttached();
        $this->parent->add($engine);

        return $this;
    }
}
