<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Establishment;

use InvalidArgumentException;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Parameter\TypeOfResult;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Establishment\Find\Request\ReadEstablishmentByEstablishmentNumberRequest;
use KboPublicSearch\Establishment\OfEnterprise\Request\ReadEstablishmentByEnterpriseNumberRequest;
use KboPublicSearch\Establishment\SearchByAuthorization\Request\ReadEstablishmentByAuthorizationRequest;
use KboPublicSearch\Establishment\SearchByName\Request\ReadEstablishmentByNameExactMatchRequest;
use KboPublicSearch\Establishment\SearchByPhoneme\Request\ReadEstablishmentByPhonemeRequest;
use KboPublicSearch\Establishment\Type\Primitive\EstablishmentName;
use KboPublicSearch\Tests\Support\Message;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class EstablishmentRequestsTest extends TestCase
{
    #[Test]
    public function find_request(): void
    {
        // Arrange
        $request = new ReadEstablishmentByEstablishmentNumberRequest(EstablishmentNumber::fromString('2147241191'), TypeOfResult::Long);

        // Assert
        $this->assertSame('ReadEstablishmentByEstablishmentNumber', $request->operation());
        $this->assertStringContainsString('<dat:EstablishmentNumber>2147241191</dat:EstablishmentNumber><mes:TypeOfResult>long</mes:TypeOfResult>', Message::xml($request));
    }

    #[Test]
    public function of_enterprise_request(): void
    {
        // Arrange
        $request = new ReadEstablishmentByEnterpriseNumberRequest(EnterpriseNumber::fromString('0314595348'), TypeOfResult::Short);

        // Assert
        $this->assertSame('ReadEstablishmentByEnterpriseNumber', $request->operation());
        $this->assertStringContainsString('<dat:EnterpriseNumber>0314595348</dat:EnterpriseNumber><mes:TypeOfResult>short</mes:TypeOfResult>', Message::xml($request));
    }

    #[Test]
    public function name_request(): void
    {
        // Arrange
        $request = new ReadEstablishmentByNameExactMatchRequest(EstablishmentName::fromString('Zuiderpoort Office Park'), Zipcode::fromString('9000'), true);

        // Assert
        $this->assertSame('ReadEstablishmentByNameExactMatch', $request->operation());
        $this->assertSame(['EstablishmentName', 'Zipcode', 'ActiveFilter'], Message::names($request));
        $this->assertSame(['EstablishmentName', 'ActiveFilter'], Message::names(new ReadEstablishmentByNameExactMatchRequest(EstablishmentName::fromString('X'), null, false)));
    }

    #[Test]
    public function phoneme_request(): void
    {
        // Arrange
        $request = new ReadEstablishmentByPhonemeRequest(Phonemes::of('City', 'Atrium'), Zipcode::fromString('1210'), true);

        // Assert
        $this->assertSame('ReadEstablishmentByPhoneme', $request->operation());
        $this->assertSame(['Phoneme', 'Phoneme', 'Zipcode', 'ActiveFilter'], Message::names($request));
    }

    #[Test]
    public function authorization_request(): void
    {
        // Arrange
        $request = new ReadEstablishmentByAuthorizationRequest(AuthorizationSearchCode::fromString('T004'), LocationFilter::municipalities('44021'), true);

        // Assert
        $this->assertSame('ReadEstablishmentByAuthorization', $request->operation());
        $this->assertSame(['AuthorizationFilter', 'LocationFilter', 'ActiveFilter'], Message::names($request));
    }

    #[Test]
    public function establishment_name_is_validated(): void
    {
        $this->expectException(InvalidArgumentException::class);

        EstablishmentName::fromString(str_repeat('a', 161));
    }
}
