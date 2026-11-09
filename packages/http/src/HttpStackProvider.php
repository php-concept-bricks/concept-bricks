<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\Http;

use Concept\Core\Container\ContainerDependency;
use Concept\Core\Http\Contracts\ArgumentResolverInterface;
use Concept\Core\Http\Contracts\RouteInterceptorInterface;
use Concept\Core\Http\Routing\Resolvers\RouteParameterArgumentResolver;
use Concept\Core\Http\Routing\Resolvers\ServerRequestArgumentResolver;
use Concept\Core\Providers\Http\HttpKernelServiceProvider;
use Concept\Extensions\CastingValinor\Contracts\CasterInterface;
use Concept\Extensions\CastingValinor\Routing\TypedRouteParameterArgumentResolver;
use Concept\Extensions\FormRequest\Contracts\FormRequestFactoryInterface;
use Concept\Extensions\FormRequest\Routing\FormRequestArgumentResolver;
use Concept\Extensions\Http\HttpServiceProvider;
use Closure;
use League\Container\DefinitionContainerInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class HttpStackProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    private const string ERR_APPENDED_RESOLVER_INVALID = 'Appended argument resolver must be a class-string, instance, or factory returning %s, got %s.';
    private const string ERR_INTERCEPTOR_INVALID = 'Route interceptor must be a class-string, instance, or factory returning %s, got %s.';

    public function __construct(
        private readonly HttpOptions $options,
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

        $container->addServiceProvider(new HttpKernelServiceProvider(
            routePaths: $this->options->routes(),
            resolvers: $this->getArgumentResolvers($container),
            interceptors: $this->normalizeInterceptors(),
            notFoundMiddleware: $this->options->notFoundMiddleware(),
        ));

        $container->addServiceProvider(new HttpServiceProvider());
    }

    /**
     * @return list<ArgumentResolverInterface>
     */
    private function getArgumentResolvers(DefinitionContainerInterface $container): array
    {
        $resolvers = [];

        if ($this->options->formRequests()) {
            $resolvers[] = new FormRequestArgumentResolver(
                formRequestFactory: fn(): FormRequestFactoryInterface => ContainerDependency::get(
                    $container,
                    FormRequestFactoryInterface::class,
                ),
                container: $container,
            );
        }

        $resolvers[] = new ServerRequestArgumentResolver();

        if ($this->options->typedRouteParameters()) {
            $resolvers[] = new TypedRouteParameterArgumentResolver(
                fn(): CasterInterface => ContainerDependency::get($container, CasterInterface::class),
            );
        }

        $resolvers[] = new RouteParameterArgumentResolver();

        foreach ($this->options->appendedResolvers() as $resolverEntry) {
            $resolvers[] = $this->resolveAppendedResolver($container, $resolverEntry);
        }

        return $resolvers;
    }

    /**
     * Core accepts instance|Closure only — wrap class-strings as lazy factories here.
     *
     * @return list<RouteInterceptorInterface|Closure(ContainerInterface): mixed>
     */
    private function normalizeInterceptors(): array
    {
        $normalized = [];

        foreach ($this->options->interceptors() as $interceptorEntry) {
            $normalized[] = $this->normalizeInterceptorEntry($interceptorEntry);
        }

        return $normalized;
    }

    /**
     * @return RouteInterceptorInterface|Closure(ContainerInterface): mixed
     */
    private function normalizeInterceptorEntry(mixed $interceptorEntry): RouteInterceptorInterface|Closure
    {
        if ($interceptorEntry instanceof RouteInterceptorInterface || $interceptorEntry instanceof Closure) {
            return $interceptorEntry;
        }

        if (!is_string($interceptorEntry) || !is_a($interceptorEntry, RouteInterceptorInterface::class, true)) {
            throw new RuntimeException(sprintf(
                self::ERR_INTERCEPTOR_INVALID,
                RouteInterceptorInterface::class,
                is_object($interceptorEntry) ? $interceptorEntry::class : get_debug_type($interceptorEntry),
            ));
        }

        /** @var class-string<RouteInterceptorInterface> $interceptorEntry */
        return static fn(ContainerInterface $container): RouteInterceptorInterface => ContainerDependency::get(
            $container,
            $interceptorEntry,
        );
    }

    private function resolveAppendedResolver(
        DefinitionContainerInterface $container,
        mixed $resolverEntry,
    ): ArgumentResolverInterface {
        if (is_string($resolverEntry) && is_a($resolverEntry, ArgumentResolverInterface::class, true)) {
            return ContainerDependency::get($container, $resolverEntry);
        }

        $resolver = $resolverEntry instanceof Closure
            ? $resolverEntry($container)
            : $resolverEntry;

        if (!$resolver instanceof ArgumentResolverInterface) {
            throw new RuntimeException(sprintf(
                self::ERR_APPENDED_RESOLVER_INVALID,
                ArgumentResolverInterface::class,
                is_object($resolver) ? $resolver::class : get_debug_type($resolver),
            ));
        }

        return $resolver;
    }
}
