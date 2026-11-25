<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise;

use InvalidArgumentException;
use KboPublicSearch\Authentication\Failure\CouldNotAuthenticate;
use KboPublicSearch\Core\Failure\CouldNotCompleteRequest;
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Failure\ValidationException;
use KboPublicSearch\Core\Http\KboResource;
use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Enterprise\Find\Failure\CouldNotFindEnterprise;
use KboPublicSearch\Enterprise\Find\Request\ReadEnterpriseRequest;
use KboPublicSearch\Enterprise\Reply\EnterpriseReply;
use KboPublicSearch\Enterprise\Reply\EnterpriseSearchReply;
use KboPublicSearch\Enterprise\SearchByAuthorization\Failure\CouldNotSearchEnterprisesByAuthorization;
use KboPublicSearch\Enterprise\SearchByAuthorization\Request\ReadEnterpriseByAuthorizationRequest;
use KboPublicSearch\Enterprise\SearchByCompanyName\Failure\CouldNotSearchEnterprisesByCompanyName;
use KboPublicSearch\Enterprise\SearchByCompanyName\Request\ReadEnterpriseLegalPersonByCompanyNameExactMatchRequest;
use KboPublicSearch\Enterprise\SearchByPersonName\Failure\CouldNotSearchEnterprisesByPersonName;
use KboPublicSearch\Enterprise\SearchByPersonName\Request\ReadEnterpriseByPhysicalPersonNameExactMatchRequest;
use KboPublicSearch\Enterprise\SearchByPhoneme\Failure\CouldNotSearchEnterprisesByPhoneme;
use KboPublicSearch\Enterprise\SearchByPhoneme\Request\ReadEnterpriseByPhonemeRequest;
use KboPublicSearch\Enterprise\Type\EnterpriseTypes;
use KboPublicSearch\Enterprise\Type\JuridicalFormFilter;
use KboPublicSearch\Enterprise\Type\Mapping\MapsEnterprises;
use KboPublicSearch\Enterprise\Type\Mapping\MapsEnterpriseSummaries;
use KboPublicSearch\Enterprise\Type\Primitive\AuthorizationCode;
use KboPublicSearch\Enterprise\Type\Primitive\CompanyName;
use KboPublicSearch\Enterprise\Type\Primitive\FirstName;
use KboPublicSearch\Enterprise\Type\Primitive\LastName;
use Webmozart\Assert\Assert;

/** @internal */
final readonly class EnterpriseResource extends KboResource
{
    use MapsEnterprises;
    use MapsEnterpriseSummaries;

    /**
     * Read an enterprise by its number (SOAP operation ReadEnterprise).
     *
     * A closed number answers with the enterprise that replaced it.
     *
     * @param EnterpriseNumber $number The enterprise number.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotFindEnterprise when the number does not exist, is canceled or is not active yet
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function find(EnterpriseNumber $number): EnterpriseReply
    {
        $result = $this->lookup(new ReadEnterpriseRequest($number), CouldNotFindEnterprise::class);

        return $this->reply(fn () => new EnterpriseReply(
            $result->context,
            $this->toEnterprise($result->body->child('Enterprise')),
        ));
    }

    /**
     * Search enterprises by the exact name of the natural person (SOAP operation ReadEnterpriseByPhysicalPersonNameExactMatch).
     *
     * @param LastName $lastName The last name.
     * @param FirstName|null $firstName The first name.
     * @param Zipcode|null $zipcode Only enterprises with this zipcode.
     * @param bool $onlyActive Only active enterprises.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEnterprisesByPersonName
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByPersonName(LastName $lastName, ?FirstName $firstName = null, ?Zipcode $zipcode = null, bool $onlyActive = true): EnterpriseSearchReply
    {
        return $this->searchEnterprises(
            new ReadEnterpriseByPhysicalPersonNameExactMatchRequest($lastName, $firstName, $zipcode, $onlyActive),
            CouldNotSearchEnterprisesByPersonName::class,
        );
    }

    /**
     * Search legal persons by their exact name (SOAP operation ReadEnterpriseLegalPersonByCompanyNameExactMatch).
     *
     * @param CompanyName $name The exact company name.
     * @param Zipcode|null $zipcode Only enterprises with this zipcode.
     * @param JuridicalFormFilter|null $juridicalForm Only enterprises with this juridical form.
     * @param bool $onlyActive Only active enterprises.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEnterprisesByCompanyName
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByCompanyName(CompanyName $name, ?Zipcode $zipcode = null, ?JuridicalFormFilter $juridicalForm = null, bool $onlyActive = true): EnterpriseSearchReply
    {
        return $this->searchEnterprises(
            new ReadEnterpriseLegalPersonByCompanyNameExactMatchRequest($name, $zipcode, $juridicalForm, $onlyActive),
            CouldNotSearchEnterprisesByCompanyName::class,
        );
    }

    /**
     * Search enterprises holding an authorization in a location (SOAP operation ReadEnterpriseByAuthorization).
     *
     * @param AuthorizationCode $code An AUTHORIZATION_SEARCH_CODE, see codeLists->read(CodeListId::AuthorizationSearchCode).
     * @param LocationFilter $location Municipalities, a municipality and its neighbours, or zipcodes.
     * @param bool $onlyActive Only active enterprises.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEnterprisesByAuthorization
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByAuthorization(AuthorizationCode $code, LocationFilter $location, bool $onlyActive = true): EnterpriseSearchReply
    {
        return $this->searchEnterprises(
            new ReadEnterpriseByAuthorizationRequest($code, $location, $onlyActive),
            CouldNotSearchEnterprisesByAuthorization::class,
        );
    }

    /**
     * Search enterprises phonetically by name (SOAP operation ReadEnterpriseByPhoneme).
     *
     * @param Phonemes $phonemes 1 to 10 words.
     * @param EnterpriseTypes $types Legal persons, natural persons or both.
     * @param Zipcode|null $zipcode Only enterprises with this zipcode.
     * @param JuridicalFormFilter|null $juridicalForm Only with EnterpriseTypes::legalPersons().
     * @param bool $onlyActive Only active enterprises.
     *
     * @throws CouldNotAuthenticate
     * @throws CouldNotCompleteRequest
     * @throws CouldNotSearchEnterprisesByPhoneme
     * @throws InvalidArgumentException when a juridical form is given for anything but legal persons only
     * @throws RequestLimitReached
     * @throws UnknownException
     */
    public function searchByPhoneme(
        Phonemes $phonemes,
        ?EnterpriseTypes $types = null,
        ?Zipcode $zipcode = null,
        ?JuridicalFormFilter $juridicalForm = null,
        bool $onlyActive = true,
    ): EnterpriseSearchReply {
        $types ??= EnterpriseTypes::all();

        Assert::true(
            $juridicalForm === null || $types->isLegalPersonsOnly(),
            'A juridical form can only be searched for together with EnterpriseTypes::legalPersons().',
        );

        return $this->searchEnterprises(
            new ReadEnterpriseByPhonemeRequest($phonemes, $types, $zipcode, $juridicalForm, $onlyActive),
            CouldNotSearchEnterprisesByPhoneme::class,
        );
    }

    /** @param class-string<ValidationException> $onFailure */
    private function searchEnterprises(Request $request, string $onFailure): EnterpriseSearchReply
    {
        $result = $this->searchFor($request, $onFailure);

        return $this->reply(fn () => new EnterpriseSearchReply($result->context, $this->toEnterpriseSummaries($result->body)));
    }
}
