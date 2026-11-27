<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit;

use KboPublicSearch\Tests\KboFixture;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Saloon\Data\RecordedResponse;
use Saloon\Helpers\Storage;

final class KboFixtureTest extends TestCase
{
    #[Test]
    public function redacts_person_names(): void
    {
        // Arrange
        $directory = sys_get_temp_dir() . '/kbo-fixture-' . bin2hex(random_bytes(4));
        $body = '<ns2:Function><ns2:Person><ns2:Surname>Doe</ns2:Surname><ns2:GivenName>An</ns2:GivenName></ns2:Person>'
            . '<ns2:Value>Antwerpen Doelstraat</ns2:Value></ns2:Function>';
        $fixture = new KboFixture('person', new Storage($directory, true));

        // Act
        $fixture->store(new RecordedResponse(200, ['Set-Cookie' => ['JSESSIONID=abc', 'ROUTEID=.7'], 'set-cookie2' => 'x', 'Content-Type' => 'text/xml'], $body));
        $stored = json_decode(file_get_contents("{$directory}/person.json"), true);

        // Assert
        $this->assertStringContainsString('<ns2:Surname>REDACTED</ns2:Surname>', $stored['data']);
        $this->assertStringContainsString('<ns2:GivenName>REDACTED</ns2:GivenName></ns2:Person>', $stored['data']);
        $this->assertStringContainsString('<ns2:Value>Antwerpen Doelstraat</ns2:Value>', $stored['data']);
        $this->assertSame('REDACTED', $stored['headers']['Set-Cookie']);
        $this->assertStringNotContainsString('JSESSIONID', json_encode($stored));
        $this->assertSame('text/xml', $stored['headers']['Content-Type']);

        unlink("{$directory}/person.json");
        rmdir($directory);
    }

    #[Test]
    public function redacts_natural_persons_enterprise_names(): void
    {
        // Arrange
        $body = '<ns2:Enterprise><ns2:Number>1</ns2:Number><ns2:TypeOfEnterprise>EPP</ns2:TypeOfEnterprise>'
            . '<ns2:Denomination><ns2:Code>001</ns2:Code><ns2:Description><ns2:Value>Doe, John</ns2:Value><ns2:Language/></ns2:Description></ns2:Denomination>'
            . '<ns2:PrimaryDenomination><ns2:Value>Doe, John</ns2:Value><ns2:Language>nl</ns2:Language></ns2:PrimaryDenomination>'
            . '<ns2:Activity><ns2:Description><ns2:Value>Consultancy</ns2:Value></ns2:Description></ns2:Activity></ns2:Enterprise>'
            . '<ns2:Enterprise><ns2:TypeOfEnterprise>ELP</ns2:TypeOfEnterprise>'
            . '<ns2:PrimaryDenomination><ns2:Value>FOD Economie</ns2:Value></ns2:PrimaryDenomination></ns2:Enterprise>';

        // Act
        $redacted = KboFixture::redact($body);

        // Assert
        $this->assertStringNotContainsString('Doe', $redacted);
        $this->assertSame(2, substr_count($redacted, '<ns2:Value>REDACTED</ns2:Value>'));
        $this->assertStringContainsString('<ns2:Value>Consultancy</ns2:Value>', $redacted);
        $this->assertStringContainsString('<ns2:Value>FOD Economie</ns2:Value>', $redacted);
    }

    #[Test]
    public function redacts_establishment_names(): void
    {
        // Arrange: establishments carry no TypeOfEnterprise, and a sole trader's establishment is often named after the person.
        $body = '<ns2:Establishment><ns2:EstablishmentNumber>2389061005</ns2:EstablishmentNumber>'
            . '<ns2:PrimaryDenomination><ns2:Value>Jane Doe</ns2:Value><ns2:Language>nl</ns2:Language></ns2:PrimaryDenomination>'
            . '<ns2:Denomination><ns2:Code>003</ns2:Code><ns2:Description><ns2:Value>Jane Doe</ns2:Value></ns2:Description></ns2:Denomination>'
            . '<ns2:Address><ns2:Street><ns2:Description><ns2:Value>Doestraat</ns2:Value></ns2:Description></ns2:Street></ns2:Address></ns2:Establishment>';

        // Act
        $redacted = KboFixture::redact($body);

        // Assert
        $this->assertStringNotContainsString('Jane', $redacted);
        $this->assertSame(2, substr_count($redacted, '<ns2:Value>REDACTED</ns2:Value>'));
        $this->assertStringContainsString('<ns2:Value>Doestraat</ns2:Value>', $redacted);
    }

    #[Test]
    public function leaves_empty_names_alone(): void
    {
        $this->assertSame('<Surname/><GivenName></GivenName>', KboFixture::redact('<Surname/><GivenName></GivenName>'));
    }
}
