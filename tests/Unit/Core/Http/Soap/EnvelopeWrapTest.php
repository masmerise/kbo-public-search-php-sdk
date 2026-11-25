<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Http\Soap;

use DateTimeImmutable;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Core\Http\Soap\Envelope;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Core\Xml\Reader;
use KboPublicSearch\Localization\Languages;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EnvelopeWrapTest extends TestCase
{
    #[Test]
    public function matches_golden_file(): void
    {
        // Act
        $xml = $this->wrap(Languages::dutch());

        // Assert
        $this->assertXmlStringEqualsXmlString(file_get_contents(__DIR__ . '/../../../../Fixtures/Cookbook/request-read-enterprise.xml'), $xml);
    }

    #[Test]
    public function languages_are_repeated(): void
    {
        // Act
        $context = Reader::fromString($this->wrap(Languages::dutchAndFrench()))->child('Header')->child('RequestContext');

        // Assert
        $languages = array_map(static fn ($e) => $e->text(), $context->children('Language'));
        $this->assertSame(['nl', 'fr'], $languages);
    }

    #[Test]
    public function french_only(): void
    {
        $this->assertSame(['fr'], Languages::french()->toArray());
    }

    private function wrap(Languages $languages): string
    {
        return Envelope::wrap(
            operation: 'ReadEnterprise',
            message: [Node::element('dat:EnterpriseNumber', '0314595348')],
            credentials: Credentials::present('userid', 'secret'),
            languages: $languages,
            now: new DateTimeImmutable('2025-09-01T11:27:10.716Z'),
            rawNonce: base64_decode('ENp2ha7j2Ar9cvWQeUybTQ=='),
            requestId: 'c1576d0a-e762-40fe-abf9-ec3f2102650b',
        );
    }
}
