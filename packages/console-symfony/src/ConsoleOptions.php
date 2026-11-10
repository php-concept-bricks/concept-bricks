<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Console;

use Closure;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

final class ConsoleOptions
{
    private string $appName = '';

    private string $appVersion = '';

    /** @var list<class-string<Command>> */
    private array $commands = [];

    private bool $appLogging = false;

    /** @var Closure(): ?LoggerInterface|null */
    private ?Closure $loggerFactory = null;

    public function appName(): string
    {
        return $this->appName;
    }

    public function setAppName(string $appName): void
    {
        $this->appName = $appName;
    }

    public function appVersion(): string
    {
        return $this->appVersion;
    }

    public function setAppVersion(string $appVersion): void
    {
        $this->appVersion = $appVersion;
    }

    /**
     * @return list<class-string<Command>>
     */
    public function commands(): array
    {
        return $this->commands;
    }

    /**
     * @param list<class-string<Command>> $commands
     */
    public function setCommands(array $commands): void
    {
        $this->commands = $commands;
    }

    public function appLogging(): bool
    {
        return $this->appLogging;
    }

    public function setAppLogging(bool $appLogging): void
    {
        $this->appLogging = $appLogging;
    }

    /**
     * @return Closure(): ?LoggerInterface|null
     */
    public function loggerFactory(): ?Closure
    {
        return $this->loggerFactory;
    }

    /**
     * @param Closure(): ?LoggerInterface $loggerFactory
     */
    public function setLoggerFactory(Closure $loggerFactory): void
    {
        $this->loggerFactory = $loggerFactory;
    }

    public function hasLoggerWiring(): bool
    {
        return $this->appLogging || $this->loggerFactory !== null;
    }
}
