<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http\Soap;

use DateTimeImmutable;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Core\Http\Soap\Security\Timestamp;
use KboPublicSearch\Core\Http\Soap\Security\UsernameToken;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Core\Xml\Writer;
use KboPublicSearch\Localization\Languages;
use SensitiveParameter;

/** @internal */
final readonly class Envelope
{
    public const array NAMESPACES = [
        'soapenv' => 'http://schemas.xmlsoap.org/soap/envelope/',
        'mes' => 'http://economie.fgov.be/kbopub/webservices/v1/messages',
        'dat' => 'http://economie.fgov.be/kbopub/webservices/v1/datamodel',
        'wsse' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecext-1.0.xsd',
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
}
