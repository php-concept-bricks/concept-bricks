<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ViewTwig;

use Concept\Extensions\ViewTwig\TwigViewServiceProvider;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class ViewTwigStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly ViewTwigOptions $options,
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
        $this->getContainer()->addServiceProvider(new TwigViewServiceProvider(
            viewsPath: $this->options->viewsPath(),
            cacheDir: $this->options->cacheDir(),
            debug: $this->options->debug(),
            defaultExtension: $this->options->defaultExtension()
                ?? TwigViewServiceProvider::DEFAULT_EXTENSION,
        ));
    }
}
