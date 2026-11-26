<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\EnterpriseOrEstablishment\Reply\EnterpriseOrEstablishmentSearchReply;
use KboPublicSearch\EnterpriseOrEstablishment\SearchByActivity\Request\ReadEnterpriseOrEstablishmentByActivityRequest;
use KboPublicSearch\EnterpriseOrEstablishment\SearchByAddress\Request\ReadEnterpriseOrEstablishmentByAddressRequest;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Enterprise;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Establishment;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Parameter\ActivityFilter;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Primitive\HouseNumber;
use KboPublicSearch\Tests\RecordedTestCase;
use PHPUnit\Framework\Attributes\Test;

final class EnterpriseOrEstablishmentTest extends RecordedTestCase
{
    #[Test]
    public function search_by_address(): void
    {
        // Arrange: ENTERPRISE is a natural person without a registered office, so FOD Economie's address is used (KBO spells it "Vooruitgangstraat").
        $this->record(ReadEnterpriseOrEstablishmentByAddressRequest::class, 'enterprise-or-establishment/search-by-address');

        // Act
        $reply = $this->kbo->enterprisesOrEstablishments->searchByAddress(Zipcode::fromString('1210'), StreetName::fromString('Vooruitgangstraat'), HouseNumber::fromString('50'));

        // Assert
        $this->assertInstanceOf(EnterpriseOrEstablishmentSearchReply::class, $reply);
        $numbers = array_map(static fn (Enterprise $e) => $e->number->toString(), $reply->enterprisesOrEstablishments->enterprises()->all());
        $this->assertContains('0314595348', $numbers);
    }

    #[Test]
    public function search_by_activity(): void
    {
        // Arrange
        $this->record(ReadEnterpriseOrEstablishmentByActivityRequest::class, 'enterprise-or-establishment/search-by-activity');

        // Act
        $reply = $this->kbo->enterprisesOrEstablishments->searchByActivity(ActivityFilter::of('10711'), LocationFilter::zipcodes('9300'));

        // Assert
        $this->assertGreaterThan(0, $reply->enterprisesOrEstablishments->count());

        foreach ($reply->enterprisesOrEstablishments as $entity) {
            $this->assertTrue($entity instanceof Enterprise || $entity instanceof Establishment);
        }
    }
}
