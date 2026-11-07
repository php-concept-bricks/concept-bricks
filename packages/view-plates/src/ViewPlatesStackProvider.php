<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ViewPlates;

use Concept\Extensions\ViewPlates\PlatesViewServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class ViewPlatesStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly ViewPlatesOptions $options,
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
        $this->getContainer()->addServiceProvider(new PlatesViewServiceProvider(
            viewsPath: $this->options->viewsPath(),
            defaultExtension: $this->options->defaultExtension()
                ?? PlatesViewServiceProvider::DEFAULT_EXTENSION,
        ));
    }
}
