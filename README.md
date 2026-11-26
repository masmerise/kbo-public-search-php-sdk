<p align="center">
<img src="https://economie.fgov.be/sites/default/files/inline-images/Entreprises/LOGO-KBO-baseline-NL-250.jpg" alt="KBO Public Search PHP SDK" height="60">
<img src="https://www.php.net/images/logos/php-logo.svg" alt="PHP" height="40">
</p>

<p align="center">
<a href="https://github.com/masmerise/kbo-public-search-php-sdk/actions"><img src="https://github.com/masmerise/kbo-public-search-php-sdk/actions/workflows/test.yml/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/masmerise/kbo-public-search-php-sdk"><img src="https://img.shields.io/packagist/dt/masmerise/kbo-public-search-php-sdk" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/masmerise/kbo-public-search-php-sdk"><img src="https://img.shields.io/packagist/v/masmerise/kbo-public-search-php-sdk" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/masmerise/kbo-public-search-php-sdk"><img src="https://img.shields.io/packagist/l/masmerise/kbo-public-search-php-sdk" alt="License"></a>
</p>

## KBO Public Search PHP SDK

This SDK provides convenient, fully-typed access to the [KBO Public Search web service](/docs/Cookbook-KBO-Public-Search-Webservice.pdf)
of the Crossroads Bank for Enterprises (Kruispuntbank van Ondernemingen).

You pass value objects and get immutable replies or typed exceptions back. SOAP, WS-Security and status codes stay
inside the SDK. It implements cookbook release R018.08 (`KboPublicSearch::VERSION`).

## Installation

You can install the package via [composer](https://getcomposer.org):

```bash
composer require masmerise/kbo-public-search-php-sdk
```

## Getting started

You can always refer to the [cookbook](/docs/Cookbook-KBO-Public-Search-Webservice.pdf) to examine the operations
and every element of their replies. Each section below names the SOAP operation and the cookbook chapter it maps to.

### Setup

Register on the [KBO Open Data portal](https://kbopub.economie.fgov.be/kbo-open-data/login) to receive your credentials.

```php
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\KboPublicSearch;

$kbo = KboPublicSearch::authenticate(
    Credentials::present(username: 'wsop-12345-1', password: 'secret'),
);
```

## Enterprises

### Read an enterprise

`ReadEnterprise`, cookbook §2.1. A closed number answers with the enterprise that replaced it.

```php
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Localization\Language;

$enterprise = $kbo->enterprises->find(EnterpriseNumber::fromString('0314.595.348'))->enterprise;

$enterprise->denominations->primary(Language::Dutch)?->name; // "Federale Overheidsdienst Economie, ..."
$enterprise->juridicalSituation->status->code;                // StatusCode::Active
$enterprise->addresses->first()?->municipality->description;  // "Sint-Joost-ten-Node"
$enterprise->activities->main();                              // the activities classified MAIN
```

### Search enterprises phonetically

`ReadEnterpriseByPhoneme`, cookbook §2.2.

```php
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Enterprise\Type\Parameter\EnterpriseJuridicalForm;
use KboPublicSearch\Enterprise\Type\Parameter\TypesOfEnterprise;

$reply = $kbo->enterprises->searchByPhoneme(
    phonemes: Phonemes::of('economie', 'energie'),
    typesOfEnterprise: TypesOfEnterprise::legalPersons(),
    enterpriseJuridicalForm: EnterpriseJuridicalForm::Other, // only allowed when searching legal persons
);

foreach ($reply->enterprises as $enterprise) {
    echo $enterprise->number, ' ', $enterprise->primaryDenomination, PHP_EOL;
}
```

### Search physical persons by name

`ReadEnterpriseByPhysicalPersonNameExactMatch`, cookbook §2.3.

```php
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Enterprise\Type\Primitive\FirstName;
use KboPublicSearch\Enterprise\Type\Primitive\LastName;

$enterprises = $kbo->enterprises->searchByPhysicalPersonName(
    lastName: LastName::fromString('Doe'),
    firstName: FirstName::fromString('John'),
    zipcode: Zipcode::fromString('3940'),
)->enterprises;
```

### Search legal persons by name

`ReadEnterpriseLegalPersonByCompanyNameExactMatch`, cookbook §2.4.

```php
use KboPublicSearch\Enterprise\Type\Primitive\CompanyName;

$enterprises = $kbo->enterprises->searchByCompanyName(
    companyName: CompanyName::fromString('Belgisch Interventie- en Restitutiebureau'),
    onlyActive: false,
)->enterprises;
```

### Search enterprises by authorization

`ReadEnterpriseByAuthorization`, cookbook §2.12. The codes come from the [code list](#code-lists).

```php
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;

$enterprises = $kbo->enterprises->searchByAuthorization(
    authorizationFilter: AuthorizationSearchCode::fromString('T004'),
    locationFilter: LocationFilter::zipcodes('9300'), // or ::municipalities() or ::neighboringMunicipality()
)->enterprises;
```

## Establishment units

Establishment replies come in two levels of detail (`TypeOfResult`, cookbook §2.5.1). `Short` returns the essentials and
the name; `Long` adds every denomination, the activities and the authorizations, but takes longer.

### Read an establishment unit

`ReadEstablishmentByEstablishmentNumber`, cookbook §2.6.

```php
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Establishment\Type\Parameter\TypeOfResult;

$establishment = $kbo->establishments->find(
    EstablishmentNumber::fromString('2.147.241.191'),
    TypeOfResult::Long,
)->establishment;

$establishment->enterpriseNumber; // the enterprise it belongs to
$establishment->activities;
```

### List the establishment units of an enterprise

`ReadEstablishmentByEnterpriseNumber`, cookbook §2.5. The list is complete, so prefer `Short` for large enterprises and
read the units you need with `find()`.

```php
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

$establishments = $kbo->establishments->ofEnterprise(EnterpriseNumber::fromString('0314.595.348'))->establishments;
```

### Search establishment units by name

`ReadEstablishmentByNameExactMatch`, cookbook §2.7.

```php
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Establishment\Type\Primitive\EstablishmentName;

$establishments = $kbo->establishments->searchByName(
    establishmentName: EstablishmentName::fromString('Zuiderpoort Office Park'),
    zipcode: Zipcode::fromString('9050'),
)->establishments;
```

### Search establishment units phonetically

`ReadEstablishmentByPhoneme`, cookbook §2.8.

```php
use KboPublicSearch\Core\Type\Parameter\Phonemes;

$establishments = $kbo->establishments->searchByPhoneme(Phonemes::of('City', 'Atrium'))->establishments;
```

### Search establishment units by authorization

`ReadEstablishmentByAuthorization`, cookbook §2.13.

```php
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;

$establishments = $kbo->establishments->searchByAuthorization(
    authorizationFilter: AuthorizationSearchCode::fromString('T074'),
    locationFilter: LocationFilter::municipalities('44021'),
)->establishments;
```

## Enterprises and establishment units together

These searches return both, in reply order. Each comes with its own `Enterprise` and `Establishment` types, which hold
the elements cookbook §2.9.2 lists.

### Search by address

`ReadEnterpriseOrEstablishmentByAddress`, cookbook §2.9. The street name must be spelled exactly; [look it up](#street-names) first.

```php
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Primitive\HouseNumber;

$results = $kbo->enterprisesOrEstablishments->searchByAddress(
    zipcode: Zipcode::fromString('1210'),
    streetName: StreetName::fromString('Vooruitgangsstraat'),
    houseNumber: HouseNumber::fromString('50'),
)->enterprisesOrEstablishments;

$results->enterprises();
$results->establishments();
```

### Search by activity

`ReadEnterpriseOrEstablishmentByActivity`, cookbook §2.11. Activity codes are NACEBEL 2025 codes or prefixes of 3 to 7 digits.

```php
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Parameter\ActivityFilter;
use KboPublicSearch\EnterpriseOrEstablishment\Type\Parameter\EnterpriseOrEstablishmentFilter;

$results = $kbo->enterprisesOrEstablishments->searchByActivity(
    activityFilter: ActivityFilter::of('10711'),
    locationFilter: LocationFilter::zipcodes('9300'),
    // all three by default
    enterpriseOrEstablishmentFilter: EnterpriseOrEstablishmentFilter::legalPersons()->with(EnterpriseOrEstablishmentFilter::establishments()),
)->enterprisesOrEstablishments;
```

## Street names

`ReadStreetName`, cookbook §2.10. Each result can be passed straight to an address search.

```php
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Street\Type\Primitive\PartOfStreetName;

$streetNames = $kbo->streets->search(
    zipcode: Zipcode::fromString('9000'),
    partOfStreetName: PartOfStreetName::fromString('veld'),
)->streetNames; // "BASSEVELDESTRAAT", "VELDSTRAAT", ...
```

## Code lists

`ReadCodeList`, cookbook §2.14. Reading a code list is free: it does not count towards your quota.

```php
use KboPublicSearch\CodeList\Type\EntityKind;

$codes = $kbo->codeLists->authorizationSearchCodes()->authorizationSearchCodes;

foreach ($codes->ofKind(EntityKind::Enterprise) as $item) {
    echo $item->code, ' ', $item->description, PHP_EOL; // "T004 Metser ( ... )"
}

$lists = $kbo->codeLists->metaCodeList()->metaCodeList; // the code lists KBO serves
```

## Replies

Every method returns a reply: the payload under a name that fits the operation, plus KBO's reply header (cookbook §1.4).

```php
use KboPublicSearch\Core\Type\Parameter\Phonemes;

$reply = $kbo->enterprises->searchByPhoneme(Phonemes::of('economie'));

$reply->context->accountBalance; // requests left today
$reply->context->snapshotDate;   // the day the data reflects; null while KBO refreshes (~00:30-02:00)
$reply->isTruncated();           // true when KBO found more than 200 results
```

A search without results returns an empty collection.

Codes in replies are enums or value objects, never plain strings, e.g. `JuridicalFormCode::FederalPublicService`.
Most enums are generated from [KBO's code tables](https://economie.fgov.be/nl/themas/ondernemingen/kruispuntbank-van/diensten-voor-administraties/codetabellen).
A code the SDK does not know yet fails the reply with an `UnknownException`: please open an issue, so it can be added.

## Failures (exception handling)

The SDK uses exceptions as its medium to communicate failures.

```
KboPublicSearchException
├── CouldNotAuthenticate
│   ├── InvalidCredentials (wrong username or password, or a skewed clock)
│   └── MustAcceptTermsOfService (exposes ->url)
├── RequestLimitReached (daily quota used up)
├── CouldNotCompleteRequest (any other SOAP fault)
├── UnknownException (connection errors, unknown codes)
└── ValidationException (KBO did not accept the request)
    ├── CouldNotFindEnterprise
    ├── CouldNotSearchEnterprisesByPhoneme
    ├── CouldNotFindEstablishment
    ├── CouldNotGetEstablishmentsOfEnterprise
    └── ...
```

```php
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Enterprise\Find\Failure\CouldNotFindEnterprise;

try {
    $enterprise = $kbo->enterprises->find(EnterpriseNumber::fromString('0206.231.995'))->enterprise;
} catch (CouldNotFindEnterprise $ex) {
    $ex->status;       // ReplyStatus::EnterpriseCanceled
    $ex->getMessage(); // "Enterprise canceled"
}
```

Invalid input never costs a request: value objects validate what the web service enforces, such as the modulo 97
check of a number, and throw an `InvalidArgumentException` as soon as they are built.

## Localization

Descriptions come in Dutch by default. Names always come in every language KBO holds.

```php
$kbo->useDutch();
$kbo->useFrench();
$kbo->useDutchAndFrench();
```

## Environment

### Production (default)

```php
$kbo->useProduction();
```

### Test

```php
$kbo->useTest();
```

Each environment has its own credentials. The test environment allows far fewer requests than production's 3000 a day.

## Clock

Every request carries a WS-Security timestamp, which KBO checks against its own clock (cookbook §1.2). Keep the clock of
the machine running the SDK in sync: KBO rejects a skewed timestamp as if the credentials were wrong.

## Timeouts

The SDK defaults to a 30 second timeout. It does not retry: a retry would fail the same way and cost another request.

## Testing

```bash
composer test
```

The tests replay real KBO replies, so they run offline. See `tests/.env.example` to record new ones.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email support@masmerise.be instead of using the issue tracker.

## Credits

- [Muhammed Sari](https://github.com/mabdullahsari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
