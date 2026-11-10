<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Console;

use Closure;
use Concept\Stack\Bricks\Logging\LoggingBrick;
use Concept\Stack\Builder\StackBuilder;
use Concept\Stack\Exceptions\InvalidCapabilityOptionsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

final class ConsoleBuilder
{
    private const string ERR_LOGGER_XOR = 'Capability "console-symfony" accepts only one of withLogging() / withCustomLogger().';

    public function __construct(
        private readonly StackBuilder $parent,
        private readonly ConsoleOptions $options,
    ) {}

    public function setName(string $appName): self
    {
        $this->options->setAppName($appName);

        return $this;
    }

    public function setVersion(string $appVersion): self
    {
        $this->options->setAppVersion($appVersion);

        return $this;
    }

    /**
     * @param list<class-string<Command>> $commands
     */
    public function setCommands(array $commands): self
    {
        $this->options->setCommands($commands);

        return $this;
    }

    /**
     * Opt-in command lifecycle logging via the shared app LoggerInterface (Logging brick).
     */
    public function withLogging(): self
    {
        $this->assertLoggerUnset();
        $this->options->setAppLogging(true);
        $this->parent->require(ConsoleBrick::NAME, LoggingBrick::NAME);

        return $this;
    }

    /**
     * Escape hatch: command lifecycle logging via a custom Psr\Log\LoggerInterface factory.
     *
     * @param Closure(): ?LoggerInterface $loggerFactory
     */
    public function withCustomLogger(Closure $loggerFactory): self
    {
        $this->assertLoggerUnset();
        $this->options->setLoggerFactory($loggerFactory);

        return $this;
    }

    private function assertLoggerUnset(): void
    {
        if ($this->options->hasLoggerWiring()) {
            throw new InvalidCapabilityOptionsException(self::ERR_LOGGER_XOR);
        }
    }
}
