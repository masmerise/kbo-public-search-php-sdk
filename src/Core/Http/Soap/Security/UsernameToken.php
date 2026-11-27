<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Http\Soap\Security;

use DateTimeImmutable;
use DateTimeZone;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Core\Xml\Node;
use SensitiveParameter;

/** @internal WS-Security UsernameToken with a PasswordDigest. */
final readonly class UsernameToken
{
    public const string FORMAT = 'Y-m-d\TH:i:s.v\Z';

    private const string PASSWORD_DIGEST = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest';

    private const string BASE64_BINARY = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';

    private function __construct(
        #[SensitiveParameter] private Credentials $credentials,
        private string $created,
        private string $rawNonce,
    ) {}

    public static function create(#[SensitiveParameter] Credentials $credentials, DateTimeImmutable $created, string $rawNonce): self
    {
        return new self($credentials, self::format($created), $rawNonce);
    }

    /** @internal */
    public static function format(DateTimeImmutable $moment): string
    {
        return $moment->setTimezone(new DateTimeZone('UTC'))->format(self::FORMAT);
    }

    public function created(): string
    {
        return $this->created;
    }

    /** base64(sha1(nonce + created + password)) */
    public function digest(): string
    {
        return base64_encode(sha1($this->rawNonce . $this->created . $this->credentials->password->toString(), true));
    }

    public function toNode(): Node
    {
        return Node::element('wsse:UsernameToken', [
            Node::element('wsse:Username', $this->credentials->username->toString()),
            Node::element('wsse:Password', $this->digest(), ['Type' => self::PASSWORD_DIGEST]),
            Node::element('wsse:Nonce', base64_encode($this->rawNonce), ['EncodingType' => self::BASE64_BINARY]),
            Node::element('wsu:Created', $this->created),
        ]);
    }
}
