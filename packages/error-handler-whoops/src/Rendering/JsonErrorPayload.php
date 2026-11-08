<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling\Rendering;

/**
 * Key names for stock JSON HTTP error payloads (withJsonRenderer / view JSON branch).
 */
final class JsonErrorPayload
{
    public const string CODE = 'code';
    public const string MESSAGE = 'message';
    public const string ERRORS = 'errors';

    /**
     * @param array<string, mixed> $errors
     * @return array{code: int, message: string, errors?: array<string, mixed>}
     */
    public static function build(int $code, string $message, array $errors = []): array
    {
        $payload = [
            self::CODE => $code,
            self::MESSAGE => $message,
        ];

        if ($errors !== []) {
            $payload[self::ERRORS] = $errors;
        }

        return $payload;
    }
}
