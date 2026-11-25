<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Http\Soap;

use DateTimeImmutable;
use DateTimeZone;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Core\Http\Soap\Security\Timestamp;
use KboPublicSearch\Core\Http\Soap\Security\UsernameToken;
use KboPublicSearch\Core\Xml\Writer;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class UsernameTokenTest extends TestCase
{
    private const array NS = [
        'wsse' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
        'wsu' => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd',
    ];

    #[Test]
    public function digest_matches_reference(): void
    {
        // Arrange
        $created = new DateTimeImmutable('2025-09-01T11:27:10.716Z');

        // Act
        $token = UsernameToken::create(Credentials::present('userid', 'secret'), $created, base64_decode('ENp2ha7j2Ar9cvWQeUybTQ=='));

        // Assert: base64(sha1(nonce . created . password)), computed once with php -r.
        $this->assertSame('2aQXMQxEmjmmXd/lDZ7S+vAIPQ0=', $token->digest());
    }

    #[Test]
    public function created_is_written_in_utc_with_milliseconds(): void
    {
        // Arrange
        $created = new DateTimeImmutable('2025-09-01T11:27:10.716Z')->setTimezone(new DateTimeZone('Europe/Brussels'));

        // Act
        $token = UsernameToken::create(Credentials::present('userid', 'secret'), $created, base64_decode('ENp2ha7j2Ar9cvWQeUybTQ=='));
        $xml = Writer::write($token->toNode(), self::NS);

        // Assert
        $this->assertSame('2025-09-01T11:27:10.716Z', $token->created());
        $this->assertSame('2aQXMQxEmjmmXd/lDZ7S+vAIPQ0=', $token->digest());
        $this->assertStringContainsString('<wsu:Created>2025-09-01T11:27:10.716Z</wsu:Created>', $xml);
        $this->assertStringContainsString('<wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">ENp2ha7j2Ar9cvWQeUybTQ==</wsse:Nonce>', $xml);
        $this->assertStringNotContainsString('secret', $xml);
    }

    #[Test]
    public function timestamp_expires_after_the_ttl(): void
    {
        // Act
        $xml = Writer::write(Timestamp::create(new DateTimeImmutable('2025-09-01T11:27:10.716Z'), 60)->toNode(), self::NS);

        // Assert
        $this->assertStringContainsString('<wsu:Created>2025-09-01T11:27:10.716Z</wsu:Created><wsu:Expires>2025-09-01T11:28:10.716Z</wsu:Expires>', $xml);
    }
}
