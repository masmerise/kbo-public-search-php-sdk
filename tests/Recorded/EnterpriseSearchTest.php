<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;
use KboPublicSearch\Enterprise\Reply\EnterpriseSearchReply;
use KboPublicSearch\Enterprise\SearchByAuthorization\Request\ReadEnterpriseByAuthorizationRequest;
use KboPublicSearch\Enterprise\SearchByCompanyName\Request\ReadEnterpriseLegalPersonByCompanyNameExactMatchRequest;
use KboPublicSearch\Enterprise\SearchByPhoneme\Request\ReadEnterpriseByPhonemeRequest;
use KboPublicSearch\Enterprise\SearchByPhysicalPersonName\Request\ReadEnterpriseByPhysicalPersonNameExactMatchRequest;
use KboPublicSearch\Enterprise\Type\EnterpriseSummary;
use KboPublicSearch\Enterprise\Type\Primitive\CompanyName;
use KboPublicSearch\Enterprise\Type\Primitive\LastName;
use KboPublicSearch\Enterprise\Type\TypeOfEnterprise;
use KboPublicSearch\Enterprise\Type\TypesOfEnterprise;
use KboPublicSearch\Tests\RecordedTestCase;
use PHPUnit\Framework\Attributes\Test;

final class EnterpriseSearchTest extends RecordedTestCase
{
    #[Test]
    public function search_by_phoneme(): void
    {
        // Arrange
        $this->record(ReadEnterpriseByPhonemeRequest::class, 'enterprise/search-by-phoneme');

        // Act
        $reply = $this->kbo->enterprises->searchByPhoneme(Phonemes::of('economie'), TypesOfEnterprise::legalPersons());

        // Assert
        $this->assertInstanceOf(EnterpriseSearchReply::class, $reply);
        $this->assertFalse($reply->isTruncated());
        $this->assertGreaterThan(0, $reply->enterprises->count());

        foreach ($reply->enterprises as $enterprise) {
            $this->assertInstanceOf(EnterpriseSummary::class, $enterprise);
            $this->assertNotEmpty($enterprise->primaryDenomination->first());
            $this->assertSame(TypeOfEnterprise::LegalPerson, $enterprise->typeOfEnterprise);
            $this->assertNotNull($enterprise->status);
        }
    }

    #[Test]
    public function search_by_phoneme_truncated(): void
    {
        // Arrange
        $this->record(ReadEnterpriseByPhonemeRequest::class, 'enterprise/search-by-phoneme-truncated');

        // Act
        $reply = $this->kbo->enterprises->searchByPhoneme(Phonemes::of('bvba'), TypesOfEnterprise::all(), onlyActive: false);

        // Assert
        $this->assertTrue($reply->isTruncated());
        $this->assertSame(200, $reply->enterprises->count());
    }

    #[Test]
    public function search_by_phoneme_empty(): void
    {
        // Arrange
        $this->record(ReadEnterpriseByPhonemeRequest::class, 'enterprise/search-by-phoneme-empty');

        // Act
        $reply = $this->kbo->enterprises->searchByPhoneme(Phonemes::of('qxzqxzqxz'));

        // Assert
        $this->assertSame(0, $reply->enterprises->count());
        $this->assertTrue($reply->context->status->isNoData());
        $this->assertFalse($reply->isTruncated());
    }

    #[Test]
    public function search_by_company_name(): void
    {
        // Arrange
        $this->record(ReadEnterpriseLegalPersonByCompanyNameExactMatchRequest::class, 'enterprise/search-by-company-name');

        // Act
        $reply = $this->kbo->enterprises->searchByCompanyName(CompanyName::fromString('Federale Overheidsdienst Economie, K.M.O., Middenstand en Energie'));

        // Assert
        $this->assertGreaterThanOrEqual(1, $reply->enterprises->count());
        $this->assertSame('0314595348', $reply->enterprises->first()?->number->toString());
    }

    #[Test]
    public function search_by_person_name(): void
    {
        // Arrange
        $this->record(ReadEnterpriseByPhysicalPersonNameExactMatchRequest::class, 'enterprise/search-by-person-name');

        // Act
        $reply = $this->kbo->enterprises->searchByPhysicalPersonName(LastName::fromString('Peeters'));

        // Assert
        $this->assertInstanceOf(EnterpriseSearchReply::class, $reply);

        foreach ($reply->enterprises as $enterprise) {
            $this->assertSame(TypeOfEnterprise::PhysicalPerson, $enterprise->typeOfEnterprise);
        }
    }

    #[Test]
    public function search_by_authorization(): void
    {
        // Arrange
        $this->record(ReadEnterpriseByAuthorizationRequest::class, 'enterprise/search-by-authorization');

        // Act
        $reply = $this->kbo->enterprises->searchByAuthorization(AuthorizationSearchCode::fromString('T004'), LocationFilter::zipcodes('9300'));

        // Assert
        $this->assertInstanceOf(EnterpriseSearchReply::class, $reply);
    }
}
