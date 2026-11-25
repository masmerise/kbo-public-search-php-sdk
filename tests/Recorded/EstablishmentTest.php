<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\Core\Reply\ReplyStatus;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Parameter\TypeOfResult;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Establishment\Find\Failure\CouldNotFindEstablishment;
use KboPublicSearch\Establishment\Find\Request\ReadEstablishmentByEstablishmentNumberRequest;
use KboPublicSearch\Establishment\OfEnterprise\Request\ReadEstablishmentByEnterpriseNumberRequest;
use KboPublicSearch\Establishment\Reply\EstablishmentReply;
use KboPublicSearch\Establishment\Reply\EstablishmentSearchReply;
use KboPublicSearch\Establishment\SearchByAuthorization\Request\ReadEstablishmentByAuthorizationRequest;
use KboPublicSearch\Establishment\SearchByName\Request\ReadEstablishmentByNameExactMatchRequest;
use KboPublicSearch\Establishment\SearchByPhoneme\Request\ReadEstablishmentByPhonemeRequest;
use KboPublicSearch\Establishment\Type\Establishment;
use KboPublicSearch\Establishment\Type\Primitive\EstablishmentName;
use KboPublicSearch\Tests\RecordedTestCase;
use PHPUnit\Framework\Attributes\Test;

final class EstablishmentTest extends RecordedTestCase
{
    #[Test]
    public function of_enterprise(): void
    {
        // Arrange
        $this->record(ReadEstablishmentByEnterpriseNumberRequest::class, 'establishment/of-enterprise');

        // Act
        $reply = $this->kbo->establishments->ofEnterprise(EnterpriseNumber::fromString(self::ENTERPRISE));

        // Assert
        $this->assertInstanceOf(EstablishmentSearchReply::class, $reply);
        $this->assertSame(self::ESTABLISHMENT, $reply->establishments->first()?->establishmentNumber->toString());
    }

    #[Test]
    public function find_short(): void
    {
        // Arrange
        $this->record(ReadEstablishmentByEstablishmentNumberRequest::class, 'establishment/find-short');

        // Act
        $reply = $this->kbo->establishments->find(EstablishmentNumber::fromString(self::ESTABLISHMENT));

        // Assert
        $this->assertInstanceOf(EstablishmentReply::class, $reply);
        $this->assertSame(self::ESTABLISHMENT, $reply->establishment->establishmentNumber->toString());
        $this->assertNotEmpty($reply->establishment->primaryDenomination->first());
    }

    #[Test]
    public function find_long(): void
    {
        // Arrange
        $this->record(ReadEstablishmentByEstablishmentNumberRequest::class, 'establishment/find-long');

        // Act
        $reply = $this->kbo->establishments->find(EstablishmentNumber::fromString(self::ESTABLISHMENT), TypeOfResult::Long);

        // Assert
        $this->assertGreaterThan(0, $reply->establishment->activities->count());
        $this->assertGreaterThan(0, $reply->establishment->denominations->count());
    }

    #[Test]
    public function find_stopped(): void
    {
        // Arrange: 2000000042 turned out to be a stopped establishment; KBO answers with the undocumented 00020.
        $this->record(ReadEstablishmentByEstablishmentNumberRequest::class, 'establishment/find-stopped');

        // Act
        try {
            $this->kbo->establishments->find(EstablishmentNumber::fromString('2000000042'));
            $this->fail('Expected CouldNotFindEstablishment.');
        } catch (CouldNotFindEstablishment $ex) {
            // Assert
            $this->assertSame(ReplyStatus::Unknown, $ex->status);
            $this->assertSame('00020', $ex->context->statusCode);
            $this->assertSame('Establishment stopped', $ex->getMessage());
            $this->assertSame(20, $ex->getCode());
        }
    }

    #[Test]
    public function search_by_name(): void
    {
        // Arrange
        $this->record(ReadEstablishmentByNameExactMatchRequest::class, 'establishment/search-by-name');

        // Act
        $reply = $this->kbo->establishments->searchByName(EstablishmentName::fromString('Masmerise'));

        // Assert
        $this->assertContains(self::ESTABLISHMENT, array_map(static fn (Establishment $e) => $e->establishmentNumber->toString(), $reply->establishments->all()));
    }

    #[Test]
    public function search_by_phoneme(): void
    {
        // Arrange
        $this->record(ReadEstablishmentByPhonemeRequest::class, 'establishment/search-by-phoneme');

        // Act
        $reply = $this->kbo->establishments->searchByPhoneme(Phonemes::of('City', 'Atrium'));

        // Assert
        $this->assertInstanceOf(EstablishmentSearchReply::class, $reply);
        $this->assertContainsOnlyInstancesOf(Establishment::class, $reply->establishments);
    }

    #[Test]
    public function search_by_authorization(): void
    {
        // Arrange
        $this->record(ReadEstablishmentByAuthorizationRequest::class, 'establishment/search-by-authorization');

        // Act
        $reply = $this->kbo->establishments->searchByAuthorization(AuthorizationSearchCode::fromString('T004'), LocationFilter::municipalities('44021'));

        // Assert
        $this->assertInstanceOf(EstablishmentSearchReply::class, $reply);
        $this->assertContainsOnlyInstancesOf(Establishment::class, $reply->establishments);
    }
}
