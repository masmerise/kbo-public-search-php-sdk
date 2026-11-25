<?php declare(strict_types=1);

namespace KboPublicSearch\Tests;

use Saloon\Data\RecordedResponse;
use Saloon\Http\Faking\Fixture;

/**
 * A recorded KBO reply under tests/Fixtures/Saloon. Delete the file to re-record (needs tests/.env).
 *
 * Only the response is stored, so the WS-Security digest never ends up on disk.
 */
final class KboFixture extends Fixture
{
    /** Natural persons' names in reply bodies; the rest of a reply is public KBO data. */
    public const array SENSITIVE_PATTERNS = [
        '/(?<=Surname>)[^<\s][^<]*/' => 'REDACTED',
        '/(?<=GivenName>)[^<\s][^<]*/' => 'REDACTED',
    ];

    /** Session and correlation headers, lowercase. */
    private const array SENSITIVE_HEADERS = ['set-cookie', 'x-correlation-id', 'x-request-id', 'x-amzn-trace-id', 'xff'];

    public function __construct(private readonly string $fixture, mixed ...$arguments)
    {
        parent::__construct($fixture, ...$arguments);
    }

    /** Replaces each match in place, unlike Saloon's regex swap, which str_replace()s a match everywhere in the body. */
    public static function redact(string $body): string
    {
        return preg_replace(array_keys(self::SENSITIVE_PATTERNS), array_values(self::SENSITIVE_PATTERNS), $body) ?? $body;
    }

    protected function beforeSave(RecordedResponse $recordedResponse): RecordedResponse
    {
        if (is_string($recordedResponse->data)) {
            $recordedResponse->data = self::redact($recordedResponse->data);
        }

        // Saloon's header swap walks into multi-value headers (Set-Cookie is a list), so it never matches them.
        foreach ($recordedResponse->headers as $name => $value) {
            if (in_array(strtolower((string) $name), self::SENSITIVE_HEADERS, true)) {
                $recordedResponse->headers[$name] = 'REDACTED';
            }
        }

        return $recordedResponse;
    }

    protected function defineName(): string
    {
        return $this->fixture;
    }
}
