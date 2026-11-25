<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\ContactInformation\ContactKind;
use KboPublicSearch\Core\Type\Mapping\MapsContactInformation;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsContactInformationTest extends TestCase
{
    use MapsContactInformation;

    #[Test]
    public function phone(): void
    {
        // Act
        $contact = $this->toContactInformations(Snippet::load('shared/contact-information'))[0];

        // Assert
        $this->assertSame('001', $contact->contactType);
        $this->assertSame(ContactKind::Phone, $contact->kind);
        $this->assertSame('Téléphone', $contact->description->in(Language::French));
        $this->assertSame('0476/000000', $contact->value);
        $this->assertFalse($contact->foreignBranchOfficeFlag);
        $this->assertSame('2017-12-24', $contact->begin?->toString());
    }

    #[Test]
    public function optional_parts_are_absent(): void
    {
        // Act
        $contact = $this->toContactInformations(Snippet::load('shared/contact-information'))[1];

        // Assert
        $this->assertNull($contact->contactType);
        $this->assertNull($contact->kind);
        $this->assertSame([], $contact->description->all());
        $this->assertSame('https://economie.fgov.be', $contact->value);
        $this->assertFalse($contact->foreignBranchOfficeFlag);
        $this->assertNull($contact->begin);
    }

    #[Test]
    public function unknown_type_keeps_the_raw_code(): void
    {
        // Act
        $contact = $this->toContactInformations(Snippet::load('shared/contact-information'))[2];

        // Assert
        $this->assertSame('009', $contact->contactType);
        $this->assertNull($contact->kind);
        $this->assertTrue($contact->foreignBranchOfficeFlag);
    }
}
