<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\ContactInformation\ContactType;
use KboPublicSearch\Core\Type\Mapping\ContactInformationMapper;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use ValueError;

final class ContactInformationMapperTest extends TestCase
{
    #[Test]
    public function phone(): void
    {
        // Act
        $contact = $this->contactInformationMapper()->toContactInformations(Snippet::load('shared/contact-information'))[0];

        // Assert
        $this->assertSame(ContactType::Phone, $contact->contactType);
        $this->assertSame('Téléphone', $contact->description->in(Language::French));
        $this->assertSame('0476/000000', $contact->value);
        $this->assertFalse($contact->foreignBranchOfficeFlag);
        $this->assertSame('2017-12-24', $contact->begin?->toString());
    }

    #[Test]
    public function optional_parts_are_absent(): void
    {
        // Act
        $contact = $this->contactInformationMapper()->toContactInformations(Snippet::load('shared/contact-information'))[1];

        // Assert
        $this->assertNull($contact->contactType);
        $this->assertSame([], $contact->description->all());
        $this->assertSame('https://economie.fgov.be', $contact->value);
        $this->assertFalse($contact->foreignBranchOfficeFlag);
        $this->assertNull($contact->begin);
    }

    #[Test]
    public function unknown_type_fails(): void
    {
        // Assert
        $this->expectException(ValueError::class);

        // Act
        $this->contactInformationMapper()->toContactInformations(Snippet::load('shared/unknown/contact-type'));
    }

    private function contactInformationMapper(): ContactInformationMapper
    {
        return new ContactInformationMapper();
    }
}
