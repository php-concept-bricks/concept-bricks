<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\View;

use Concept\Core\Container\ContainerDependency;
use Concept\Core\Http\Contracts\RequestContextInterface;
use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Concept\Extensions\View\Contracts\ViewInterface;
use Concept\Extensions\View\ViewServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class ViewStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly ViewOptions $options,
    ) {
        $this->options->checkOptions();
    }

    public function provides(string $id): bool
    {
        return false;
    }

    public function register(): void
    {
    }

    public function boot(): void
    {
        $container = $this->getContainer();

        $container->addServiceProvider(new ViewServiceProvider(
            responseFactoryFactory: fn(): ResponseFactoryInterface => ContainerDependency::get(
                $container,
                ResponseFactoryInterface::class,
            ),
            viewFactory: fn(): ViewInterface => ContainerDependency::get($container, ViewInterface::class),
            requestContextFactory: fn(): RequestContextInterface => ContainerDependency::get(
                $container,
                RequestContextInterface::class,
            ),
            paths: $this->options->paths(),
            extensions: $this->options->extensions(),
            routeNamespace: $this->options->routeNamespaces(),
        ));
    }
}
