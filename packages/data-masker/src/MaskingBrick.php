<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Masking;

use Concept\Stack\Contract\BrickInterface;
use League\Container\ServiceProvider\ServiceProviderInterface;

/**
 * Entry point for the data-masker capability.
 *
 *   $stack->add(new MaskingBrick())
 *       ->builder()
 *       ->setPatterns([...]);
 */
final class MaskingBrick implements BrickInterface
{
    public const string NAME = 'data-masker';

    private readonly MaskingOptions $options;

    public function __construct()
    {
        $this->options = new MaskingOptions();
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function requires(): array
    {
        return [];
    }

    public function builder(): MaskingBuilder
    {
        return new MaskingBuilder($this->options);
    }

    /**
     * @return list<ServiceProviderInterface>
     */
    public function providers(): array
    {
        return [new MaskingStackProvider($this->options)];
    }
}
