<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Enterprise;

use InvalidArgumentException;
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Enterprise\SearchByAuthorization\Request\ReadEnterpriseByAuthorizationRequest;
use KboPublicSearch\Enterprise\SearchByCompanyName\Request\ReadEnterpriseLegalPersonByCompanyNameExactMatchRequest;
use KboPublicSearch\Enterprise\SearchByPersonName\Request\ReadEnterpriseByPhysicalPersonNameExactMatchRequest;
use KboPublicSearch\Enterprise\SearchByPhoneme\Request\ReadEnterpriseByPhonemeRequest;
use KboPublicSearch\Enterprise\Type\EnterpriseTypes;
use KboPublicSearch\Enterprise\Type\JuridicalFormFilter;
use KboPublicSearch\Enterprise\Type\Primitive\AuthorizationCode;
use KboPublicSearch\Enterprise\Type\Primitive\CompanyName;
use KboPublicSearch\Enterprise\Type\Primitive\FirstName;
use KboPublicSearch\Enterprise\Type\Primitive\LastName;
use KboPublicSearch\KboPublicSearch;
use KboPublicSearch\Tests\Support\Message;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

final class EnterpriseSearchRequestsTest extends TestCase
{
    #[Test]
    public function phoneme_request_follows_the_xsd_order(): void
    {
        // Arrange
        $request = new ReadEnterpriseByPhonemeRequest(Phonemes::of('a', 'b'), EnterpriseTypes::legalPersons(), Zipcode::fromString('1210'), JuridicalFormFilter::Other, true);

        // Act
        $xml = Message::xml($request);

        // Assert
        $this->assertSame('ReadEnterpriseByPhoneme', $request->operation());
        $this->assertSame(['Phoneme', 'Phoneme', 'Zipcode', 'TypeOfEnterprise', 'EnterpriseJuridicalForm', 'ActiveFilter'], Message::names($request));
        $this->assertStringContainsString('<mes:Phoneme>a</mes:Phoneme><mes:Phoneme>b</mes:Phoneme><mes:Zipcode>1210</mes:Zipcode><mes:TypeOfEnterprise>ELP</mes:TypeOfEnterprise><mes:EnterpriseJuridicalForm>OVE</mes:EnterpriseJuridicalForm><mes:ActiveFilter>true</mes:ActiveFilter>', $xml);
    }

    #[Test]
    public function phoneme_request_omits_optional_elements(): void
    {
        // Arrange
        $request = new ReadEnterpriseByPhonemeRequest(Phonemes::of('a'), EnterpriseTypes::all(), null, null, false);

        // Act
        $xml = Message::xml($request);

        // Assert
        $this->assertSame(['Phoneme', 'TypeOfEnterprise', 'TypeOfEnterprise', 'ActiveFilter'], Message::names($request));
        $this->assertStringContainsString('<mes:TypeOfEnterprise>ELP</mes:TypeOfEnterprise><mes:TypeOfEnterprise>EPP</mes:TypeOfEnterprise><mes:ActiveFilter>false</mes:ActiveFilter>', $xml);
    }

    #[Test]
    public function person_name_request_follows_the_xsd_order(): void
    {
        // Arrange
        $request = new ReadEnterpriseByPhysicalPersonNameExactMatchRequest(LastName::fromString('Doe'), FirstName::fromString('John'), Zipcode::fromString('9000'), true);

        // Assert
        $this->assertSame('ReadEnterpriseByPhysicalPersonNameExactMatch', $request->operation());
        $this->assertSame(['LastName', 'FirstName', 'Zipcode', 'ActiveFilter'], Message::names($request));
        $this->assertSame(['LastName', 'ActiveFilter'], Message::names(new ReadEnterpriseByPhysicalPersonNameExactMatchRequest(LastName::fromString('Doe'), null, null, true)));
    }

    #[Test]
    public function company_name_request_follows_the_xsd_order(): void
    {
        // Arrange
        $request = new ReadEnterpriseLegalPersonByCompanyNameExactMatchRequest(CompanyName::fromString('Acme'), Zipcode::fromString('9000'), JuridicalFormFilter::PrivateCompany, true);

        // Assert
        $this->assertSame('ReadEnterpriseLegalPersonByCompanyNameExactMatch', $request->operation());
        $this->assertSame(['CompanyName', 'Zipcode', 'EnterpriseJuridicalForm', 'ActiveFilter'], Message::names($request));
        $this->assertStringContainsString('<mes:EnterpriseJuridicalForm>610</mes:EnterpriseJuridicalForm>', Message::xml($request));
    }

    #[Test]
    public function authorization_request_follows_the_xsd_order(): void
    {
        // Arrange
        $request = new ReadEnterpriseByAuthorizationRequest(AuthorizationCode::fromString('T004'), LocationFilter::zipcodes('9300'), false);

        // Assert
        $this->assertSame('ReadEnterpriseByAuthorization', $request->operation());
        $this->assertSame(['AuthorizationFilter', 'LocationFilter', 'ActiveFilter'], Message::names($request));
        $this->assertStringContainsString('<mes:AuthorizationFilter>T004</mes:AuthorizationFilter><mes:LocationFilter><mes:ZipcodeLocationFilter><mes:Zipcode>9300</mes:Zipcode>', Message::xml($request));
    }

    #[Test]
    public function juridical_form_requires_legal_persons_only(): void
    {
        // Arrange
        $mock = MockClient::global([MockResponse::make('', 500)]);
        $kbo = KboPublicSearch::authenticate(Credentials::present('userid', 'secret'));

        // Act
        try {
            $kbo->enterprises->searchByPhoneme(Phonemes::of('economie'), EnterpriseTypes::all(), juridicalForm: JuridicalFormFilter::Other);
            $this->fail('Expected InvalidArgumentException.');
        } catch (InvalidArgumentException) {
            // Assert
            $mock->assertNothingSent();
        }
    }

    #[Test]
    public function names_and_codes_are_validated(): void
    {
        foreach ([
            fn () => LastName::fromString(''),
            fn () => FirstName::fromString(str_repeat('a', 161)),
            fn () => CompanyName::fromString(''),
            fn () => AuthorizationCode::fromString('T04'),
            fn () => AuthorizationCode::fromString('T0045'),
        ] as $build) {
            try {
                $build();
                $this->fail('Expected InvalidArgumentException.');
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}
