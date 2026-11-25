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
    public function leaves_empty_names_alone(): void
    {
        $this->assertSame('<Surname/><GivenName></GivenName>', KboFixture::redact('<Surname/><GivenName></GivenName>'));
    }
}
