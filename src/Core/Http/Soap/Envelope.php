<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http\Soap;

use DateTimeImmutable;
use InvalidArgumentException;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Core\Failure\KboPublicSearchException;
use KboPublicSearch\Core\Failure\Mapping\MapsSoapFaults;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\Mapping\ResultMapper;
use KboPublicSearch\Core\Http\Result;
use KboPublicSearch\Core\Http\Soap\Security\Timestamp;
use KboPublicSearch\Core\Http\Soap\Security\UsernameToken;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Core\Xml\Reader;
use KboPublicSearch\Core\Xml\Writer;
use KboPublicSearch\Localization\Languages;
use RuntimeException;
use SensitiveParameter;

/** @internal */
final readonly class Envelope
{
    use MapsSoapFaults;

    public const array NAMESPACES = [
        'soapenv' => 'http://schemas.xmlsoap.org/soap/envelope/',
        'mes' => 'http://economie.fgov.be/kbopub/webservices/v1/messages',
        'dat' => 'http://economie.fgov.be/kbopub/webservices/v1/datamodel',
        'wsse' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
        'wsu' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd',
    ];

    /** @param list<Node> $message The children of mes:{operation}Request, in XSD sequence order. */
    public static function wrap(
        string $operation,
        array $message,
        #[SensitiveParameter] Credentials $credentials,
        Languages $languages,
        DateTimeImmutable $now,
        string $rawNonce,
        string $requestId,
    ): string {
        $envelope = Node::element('soapenv:Envelope', [
            Node::element('soapenv:Header', [
                Node::element('wsse:Security', [
                    Timestamp::create($now)->toNode(),
                    UsernameToken::create($credentials, $now, $rawNonce)->toNode(),
                ], ['soapenv:mustUnderstand' => '1']),
                Node::element('mes:RequestContext', [
                    Node::element('mes:Id', $requestId),
                    ...array_map(static fn (string $language) => Node::element('mes:Language', $language), $languages->toArray()),
                ]),
            ]),
            Node::element('soapenv:Body', [
                Node::element("mes:{$operation}Request", $message),
            ]),
        ]);

        return Writer::write($envelope, self::NAMESPACES);
    }

    /**
     * @throws KboPublicSearchException a typed failure for a SOAP fault, UnknownException for anything that is not a KBO reply
     */
    public static function unwrap(string $xml): Result
    {
        try {
            $envelope = Reader::fromString($xml);

            if ($envelope->name() !== 'Envelope') {
                throw new RuntimeException('Response is not a SOAP envelope.');
            }

            $fault = $envelope->optional('Body')?->optional('Fault');
        } catch (RuntimeException $ex) {
            throw UnknownException::sorry($ex);
        }

        if ($fault !== null) {
            throw self::toException(new Fault(
                code: $fault->optionalText('faultcode') ?? '',
                string: preg_replace('/\s+/', ' ', $fault->optionalText('faultstring') ?? '') ?? '',
            ));
        }

        try {
            return new ResultMapper()($envelope);
        } catch (InvalidArgumentException|RuntimeException $ex) {
            throw UnknownException::sorry($ex);
        }
    }
}
