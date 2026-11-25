<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\HouseNumber;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Enterprise\Type\EnterpriseSummary;
use KboPublicSearch\Entity\Reply\EntitySearchReply;
use KboPublicSearch\Entity\SearchByActivity\Request\ReadEnterpriseOrEstablishmentByActivityRequest;
use KboPublicSearch\Entity\SearchByAddress\Request\ReadEnterpriseOrEstablishmentByAddressRequest;
use KboPublicSearch\Entity\Type\ActivityFilter;
use KboPublicSearch\Establishment\Type\Establishment;
use KboPublicSearch\Tests\RecordedTestCase;
use PHPUnit\Framework\Attributes\Test;

final class EntityTest extends RecordedTestCase
{
    #[Test]
    public function search_by_address(): void
    {
        // Arrange: ENTERPRISE is a natural person without a registered office, so FOD Economie's address is used (KBO spells it "Vooruitgangstraat").
        $this->record(ReadEnterpriseOrEstablishmentByAddressRequest::class, 'entity/search-by-address');

        // Act
        $reply = $this->kbo->entities->searchByAddress(Zipcode::fromString('1210'), StreetName::fromString('Vooruitgangstraat'), HouseNumber::fromString('50'));

        // Assert
        $this->assertInstanceOf(EntitySearchReply::class, $reply);
        $numbers = array_map(static fn (EnterpriseSummary $e) => $e->number->toString(), $reply->entities->enterprises()->all());
        $this->assertContains('0314595348', $numbers);
    }

    #[Test]
    public function search_by_activity(): void
    {
        // Arrange
        $this->record(ReadEnterpriseOrEstablishmentByActivityRequest::class, 'entity/search-by-activity');

        // Act
        $reply = $this->kbo->entities->searchByActivity(ActivityFilter::of('10711'), LocationFilter::zipcodes('9300'));

        // Assert
        $this->assertGreaterThan(0, $reply->entities->count());

        foreach ($reply->entities as $entity) {
            $this->assertTrue($entity instanceof EnterpriseSummary || $entity instanceof Establishment);
        }
    }
}
