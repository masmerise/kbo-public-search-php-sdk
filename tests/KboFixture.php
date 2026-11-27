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

    /**
     * Replaces each match in place, unlike Saloon's regex swap, which str_replace()s a match everywhere in the body.
     *
     * The name of a natural person's enterprise (EPP) is the person's name, so those denominations are redacted too,
     * as are all establishment names.
     */
    public static function redact(string $body): string
    {
        $body = preg_replace(array_keys(self::SENSITIVE_PATTERNS), array_values(self::SENSITIVE_PATTERNS), $body) ?? $body;

        $names = static fn (string $block) => preg_replace('/(<(?:\w+:)?(?:Primary)?Denomination>.*?<(?:\w+:)?Value>)[^<]+/s', '$1REDACTED', $block) ?? $block;

        $body = preg_replace_callback(
            '/<(\w+:)?Enterprise>.*?<\/\1Enterprise>/s',
            static fn (array $enterprise) => preg_match('/TypeOfEnterprise>EPP</', $enterprise[0]) === 1 ? $names($enterprise[0]) : $enterprise[0],
            $body,
        ) ?? $body;

        // Establishments carry no TypeOfEnterprise, and a sole trader's establishment is often named after the person.
        return preg_replace_callback('/<(\w+:)?Establishment>.*?<\/\1Establishment>/s', static fn (array $establishment) => $names($establishment[0]), $body) ?? $body;
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
