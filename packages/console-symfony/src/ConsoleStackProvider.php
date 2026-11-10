<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Console;

use Concept\Extensions\ConsoleSymfony\ConsoleSymfonyServiceProvider;
use Concept\Extensions\LoggerMonolog\Contracts\LoggerInterface;
use Concept\Stack\Support\OptionalDependency;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

final class ConsoleStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function __construct(
        private readonly ConsoleOptions $options,
    ) {}

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

        $loggerFactory = $this->options->loggerFactory();
        if ($loggerFactory === null && $this->options->appLogging()) {
            $loggerFactory = OptionalDependency::factory($container, LoggerInterface::class);
        }

        $container->addServiceProvider(new ConsoleSymfonyServiceProvider(
            appName: $this->options->appName(),
            appVersion: $this->options->appVersion(),
            commands: $this->options->commands(),
            loggerFactory: $loggerFactory,
        ));
    }
}
