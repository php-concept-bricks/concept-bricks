<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling\Rendering;

/**
 * Path / naming recipe for ViewHttpErrorRenderer (stack glue → renderer).
 */
final readonly class ViewHttpErrorRenderOptions
{
    /**
     * @param string $fallbackPath Absolute dir with PHP fallback files ({code}.php).
     *                             Empty string disables PHP fallback (critical HTML only).
     * @param string $defaultNamespace View namespace when ViewRouteNamespaceResolver returns null
     *                                 (empty view.route_namespace map). Must exist in view.paths.
     * @param string $templateFormat sprintf pattern: namespace, HTTP status code
     *                               → e.g. @frontend/errors/404
     */
    public function __construct(
        public string $fallbackPath = '',
        public string $defaultNamespace = 'frontend',
        public string $templateFormat = '@%s/errors/%d',
    ) {}
}
