<p align="center">
<a href="https://github.com/masmerise/kbo-public-search-php-sdk/actions"><img src="https://github.com/masmerise/kbo-public-search-php-sdk/actions/workflows/test.yml/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/masmerise/kbo-public-search-php-sdk"><img src="https://img.shields.io/packagist/dt/masmerise/kbo-public-search-php-sdk" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/masmerise/kbo-public-search-php-sdk"><img src="https://img.shields.io/packagist/v/masmerise/kbo-public-search-php-sdk" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/masmerise/kbo-public-search-php-sdk"><img src="https://img.shields.io/packagist/l/masmerise/kbo-public-search-php-sdk" alt="License"></a>
</p>

## KBO Public Search PHP SDK

This SDK provides convenient, fully-typed access to the [KBO/BCE Public Search web service](/docs/Cookbook-KBO-Public-Search-Webservice.pdf)
of the Crossroads Bank for Enterprises (Kruispuntbank van Ondernemingen).

You call methods with value objects and get immutable replies or typed exceptions back.
SOAP, WS-Security, XML namespaces and status codes stay inside the SDK.

## Installation

You can install the package via [composer](https://getcomposer.org):

```bash
composer require masmerise/kbo-public-search-php-sdk
```

## Getting started

You can always refer to the [cookbook](/docs/Cookbook-KBO-Public-Search-Webservice.pdf) to examine the operations that are available.
Every method's docblock names the SOAP operation it calls.

### Setup

Register for the web service on the [KBO Open Data portal](https://kbopub.economie.fgov.be/kbo-open-data/login) to receive your credentials.

```php
use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\KboPublicSearch;

$kbo = KboPublicSearch::authenticate(
    Credentials::present(username: 'wsop-12345-1', password: 'secret'),
);
```

### Environments

The SDK targets the production environment by default. You can switch to the test (acceptance) environment when needed:

```php
$kbo->useTest();        // https://kbopub-acc.economie.fgov.be
$kbo->useProduction();  // https://kbopub.economie.fgov.be
```

Both switches are fluent and affect every subsequent request. Each environment has its own credentials.

### Languages

Descriptions (statuses, juridical forms, activities, ...) are returned in Dutch by default:

```php
$kbo->useDutch();
$kbo->useFrench();
$kbo->useDutchAndFrench();
```

Names (denominations) always come in every language KBO holds, German included.

```php
use KboPublicSearch\Localization\Language;

$name = $enterprise->denominations->primary(Language::French)?->name;
$status = $enterprise->juridicalSituation->status->description->in(Language::Dutch);
```

### Enterprises

```php
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Enterprise\Type\TypesOfEnterprise;
use KboPublicSearch\Enterprise\Type\EnterpriseJuridicalForm;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;
use KboPublicSearch\Enterprise\Type\Primitive\CompanyName;
use KboPublicSearch\Enterprise\Type\Primitive\FirstName;
use KboPublicSearch\Enterprise\Type\Primitive\LastName;

$enterprise = $kbo->enterprises->find(
    EnterpriseNumber::fromString('0314.595.348'), // also "BE0314595348", "314595348"
)->enterprise;

$enterprise->number->toString();               // "0314595348"
$enterprise->denominations->primary()?->name;  // the first name, in any language
$enterprise->juridicalSituation->status->code; // StatusCode::Active
$enterprise->addresses->first()?->street->description->first();
$enterprise->functions;                        // Functions
$enterprise->activities->main();               // activities classified MAIN

// At most 200 summaries; $reply->isTruncated() when KBO found more.
$reply = $kbo->enterprises->searchByPhoneme(
    phonemes: Phonemes::of('economie', 'energie'),
    typesOfEnterprise: TypesOfEnterprise::legalPersons(), // ::physicalPersons(), ::all() (default)
    zipcode: Zipcode::fromString('1210'),
    enterpriseJuridicalForm: EnterpriseJuridicalForm::Other, // only together with legalPersons()
    onlyActive: true,
);

$reply = $kbo->enterprises->searchByPhysicalPersonName(LastName::fromString('Doe'), firstName: FirstName::fromString('John'));
$reply = $kbo->enterprises->searchByCompanyName(CompanyName::fromString('Federale Overheidsdienst Economie, K.M.O., Middenstand en Energie'));
$reply = $kbo->enterprises->searchByAuthorization(AuthorizationSearchCode::fromString('T004'), LocationFilter::zipcodes('9300'));

foreach ($reply->enterprises as $summary) {
    echo $summary->number, ' ', $summary->primaryDenomination, PHP_EOL;
}
```

A location filter is 1 to 4 municipality (NIS) codes, 1 municipality and its neighbours, or 1 to 4 zipcodes:

```php
use KboPublicSearch\Core\Type\Parameter\LocationFilter;

LocationFilter::municipalities('44021', '44020');
LocationFilter::neighbouringMunicipality('44021');
LocationFilter::zipcodes('9000', '9050');
```

### Establishments

```php
use KboPublicSearch\Establishment\Type\TypeOfResult;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Core\Type\Primitive\AuthorizationSearchCode;
use KboPublicSearch\Establishment\Type\Primitive\EstablishmentName;

$establishment = $kbo->establishments->find(EstablishmentNumber::fromString('2.147.241.191'), TypeOfResult::Long)->establishment;

$kbo->establishments->ofEnterprise(EnterpriseNumber::fromString('0314595348'), TypeOfResult::Short);
$kbo->establishments->searchByName(EstablishmentName::fromString('Zuiderpoort Office Park'));
$kbo->establishments->searchByPhoneme(Phonemes::of('City', 'Atrium'));
$kbo->establishments->searchByAuthorization(AuthorizationSearchCode::fromString('T004'), LocationFilter::municipalities('44021'));
```

`TypeOfResult::Short` returns the name in the requested languages (`$establishment->primaryDenomination`). `TypeOfResult::Long` returns every
denomination, the activities, the authorizations and the enterprise's ex officio striking offs instead. Whatever a level
does not return is an empty collection. Long replies are slow for enterprises with many establishments, so prefer
`ofEnterprise(..., TypeOfResult::Short)` followed by `find(..., TypeOfResult::Long)` for the ones you need.

### Enterprises and establishments together

```php
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Entity\Type\Primitive\HouseNumber;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Entity\Type\ActivityFilter;
use KboPublicSearch\Entity\Type\EntityFilter;

$entities = $kbo->entities->searchByAddress(
    zipcode: Zipcode::fromString('1210'),
    streetName: StreetName::fromString('Vooruitgangstraat'),
    houseNumber: HouseNumber::fromString('50'),
)->entities;

$entities->enterprises();     // Entity\Type\Enterprises
$entities->establishments();  // Entity\Type\Establishments

$kbo->entities->searchByActivity(
    activityFilter: ActivityFilter::of('10711'),  // NACEBEL 2025 codes or prefixes (3-7 digits)
    locationFilter: LocationFilter::zipcodes('9300'),
    entityFilter: EntityFilter::legalPersons()->with(EntityFilter::establishments()), // ::all() (default), ::physicalPersons()
);
```

### Street names and code lists

```php
use KboPublicSearch\CodeList\Type\EntityKind;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Entity\Type\Primitive\HouseNumber;
use KboPublicSearch\Street\Type\Primitive\PartOfStreetName;

// The exact spelling an address search needs (cookbook §2.9.1); each name is a StreetName.
$street = $kbo->streets->search(Zipcode::fromString('9000'), PartOfStreetName::fromString('veld'))->streetNames->first();
$kbo->entities->searchByAddress(Zipcode::fromString('9000'), $street, HouseNumber::fromString('1'));

$codes = $kbo->codeLists->authorizationSearchCodes()->authorizationSearchCodes;
$lists = $kbo->codeLists->metaCodeList()->metaCodeList; // which lists exist

foreach ($codes->ofKind(EntityKind::Enterprise) as $item) {
    $kbo->enterprises->searchByAuthorization($item->code, LocationFilter::zipcodes('9300'));
}
```

Reading a code list is free: it does not count towards your quota.

## Replies

Every operation returns a reply: the payload under a name that fits the operation, plus the reply's header.

```php
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

$reply = $kbo->enterprises->find(EnterpriseNumber::fromString('0314.595.348'));

$reply->enterprise;                 // Enterprise
$reply->context->requestId;         // the UUID the SDK sent
$reply->context->replyId;           // the UUID KBO generated
$reply->context->status;            // ReplyStatus::Success
$reply->context->accountBalance;    // ?int, requests left today
$reply->context->snapshotDate;      // ?Date, the day the data reflects; null while KBO refreshes (~00:30-02:00)
$reply->context->charged();         // false for free replies (10001/10003)
```

| Method                                            | Reply                      | Payload                                  |
|---------------------------------------------------|----------------------------|------------------------------------------|
| `enterprises->find()`                             | `EnterpriseReply`          | `enterprise: Enterprise`                 |
| `enterprises->searchBy*()`                        | `EnterpriseSearchReply`    | `enterprises: EnterpriseSummaries`       |
| `establishments->find()`                          | `EstablishmentReply`       | `establishment: Establishment`           |
| `establishments->ofEnterprise()`                  | `EstablishmentsOfEnterpriseReply` | `establishments: Establishments`  |
| `establishments->searchBy*()`                     | `EstablishmentSearchReply` | `establishments: Establishments`         |
| `entities->searchBy*()`                           | `EntitySearchReply`        | `entities: Entities`                     |
| `streets->search()`                               | `StreetNameReply`          | `streetNames: StreetNames`               |
| `codeLists->authorizationSearchCodes()`           | `AuthorizationSearchCodesReply` | `authorizationSearchCodes: AuthorizationSearchCodes` |
| `codeLists->metaCodeList()`                       | `MetaCodeListReply`        | `metaCodeList: MetaCodeList`             |

Searches return at most 200 results. When KBO found more, `$reply->isTruncated()` is `true`.
A search without matches returns an empty collection rather than throwing. `ofEnterprise()` is not a search: its list
is never truncated, and an enterprise without active establishment units yields an empty list.

## Codes

Codes in replies are typed, never plain strings:

```php
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Enterprise\Type\JuridicalFormCode;

$enterprise = $kbo->enterprises->find(EnterpriseNumber::fromString('0314.595.348'))->enterprise;

$enterprise->juridicalForm?->code === JuridicalFormCode::FederalPublicService;
$enterprise->juridicalForm?->code->value; // "301"
$enterprise->juridicalForm?->description; // the description in the requested language(s)
```

Most codes are enums generated from [KBO's code tables](https://economie.fgov.be/nl/themas/ondernemingen/kruispuntbank-van/diensten-voor-administraties/codetabellen).
Codes from tables that grow often or are too large for an enum are value objects instead: `AuthorizationCode`,
`QualificationCode`, `MunicipalityCode`, `NacebelCode` and `StreetCode`.

A reply holding a code the SDK does not know yet throws an `UnknownException`; so does a reply status the SDK does not
know. Please open an issue: the enums are
regenerated from the code tables (see `docs/codes`) and released as soon as possible.

## Failures (exception handling)

The SDK uses exceptions as its medium to communicate failures.

```
KboPublicSearchException
├── CouldNotAuthenticate
│   ├── InvalidCredentials (wrong username or password, or a skewed clock)
│   └── MustAcceptTermsOfService (exposes ->url)
├── RequestLimitReached (daily quota used up)
├── CouldNotCompleteRequest (any other SOAP fault, exposes ->faultCode and ->faultString)
├── UnknownException (connection errors, replies that are not KBO replies)
└── ValidationException (KBO answered with a status this operation does not accept)
    ├── CouldNotFindEnterprise
    ├── CouldNotSearchEnterprisesByPhoneme
    ├── CouldNotSearchEnterprisesByPhysicalPersonName
    ├── CouldNotSearchEnterprisesByCompanyName
    ├── CouldNotSearchEnterprisesByAuthorization
    ├── CouldNotFindEstablishment
    ├── CouldNotGetEstablishmentsOfEnterprise
    ├── CouldNotSearchEstablishmentsByName
    ├── CouldNotSearchEstablishmentsByPhoneme
    ├── CouldNotSearchEstablishmentsByAuthorization
    ├── CouldNotSearchEntitiesByAddress
    ├── CouldNotSearchEntitiesByActivity
    ├── CouldNotSearchStreets
    └── CouldNotReadCodeList
```

A `ValidationException` carries the reply's status and context. Unknown, canceled and not-yet-active numbers throw:

```php
use KboPublicSearch\Core\Reply\ReplyStatus;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Enterprise\Find\Failure\CouldNotFindEnterprise;

try {
    $enterprise = $kbo->enterprises->find(EnterpriseNumber::fromString('0206231995'))->enterprise;
} catch (CouldNotFindEnterprise $ex) {
    $ex->status;              // ReplyStatus::EnterpriseCanceled
    $ex->getMessage();        // "Enterprise canceled"
    $ex->status->value;       // "00014"
}
```

Invalid input never costs a request: value objects validate what the schema enforces (mod-97 checks, lengths,
counts) and throw an `InvalidArgumentException` as soon as they are built.

```php
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

EnterpriseNumber::fromString('0314.595.349'); // InvalidArgumentException: does not pass the modulo 97 check
```

## Quota

Production allows 3000 requests a day, reset daily. Replies tell you how many are left:

```php
use KboPublicSearch\Core\Failure\RequestLimitReached;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

try {
    $reply = $kbo->enterprises->find(EnterpriseNumber::fromString('0314595348'));
    $left = $reply->context->accountBalance;
} catch (RequestLimitReached $ex) {
    $ex->getMessage(); // "Quota Exceeded!" or "Your balance is empty!"
}
```

The test environment allows far fewer requests (100 a week at the time of writing).

## Clock sync

Every request carries a WS-Security timestamp, which KBO checks against its own clock.
Keep the clock of the machine running the SDK synchronised with a time server.

KBO answers a rejected timestamp with the same generic security fault as wrong credentials, so a skewed clock
surfaces as `InvalidCredentials`. When credentials that worked before start failing, check the clock first.

## Terms of service

KBO may ask you to accept new terms of service before it answers again:

```php
use KboPublicSearch\Authentication\Failure\MustAcceptTermsOfService;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

try {
    $kbo->enterprises->find(EnterpriseNumber::fromString('0314595348'));
} catch (MustAcceptTermsOfService $ex) {
    $ex->url; // "https://kbopub.economie.fgov.be/kbo-open-data/login"
}
```

## Timeouts

The SDK defaults to a 30 second timeout and does not retry: SOAP faults are deterministic and retries would spend quota.

## Version

The SDK implements cookbook release R018.08:

```php
use KboPublicSearch\KboPublicSearch;

KboPublicSearch::VERSION; // "R018.08"
```

## Implementation progress

| SOAP operation                                   | Method                                   |   |
|--------------------------------------------------|------------------------------------------|---|
| ReadEnterprise                                   | `enterprises->find()`                    | ✓ |
| ReadEnterpriseByPhoneme                          | `enterprises->searchByPhoneme()`         | ✓ |
| ReadEnterpriseByPhysicalPersonNameExactMatch     | `enterprises->searchByPhysicalPersonName()`      | ✓ |
| ReadEnterpriseLegalPersonByCompanyNameExactMatch | `enterprises->searchByCompanyName()`     | ✓ |
| ReadEnterpriseByAuthorization                    | `enterprises->searchByAuthorization()`   | ✓ |
| ReadEstablishmentByEstablishmentNumber           | `establishments->find()`                 | ✓ |
| ReadEstablishmentByEnterpriseNumber              | `establishments->ofEnterprise()`         | ✓ |
| ReadEstablishmentByNameExactMatch                | `establishments->searchByName()`         | ✓ |
| ReadEstablishmentByPhoneme                       | `establishments->searchByPhoneme()`      | ✓ |
| ReadEstablishmentByAuthorization                 | `establishments->searchByAuthorization()` | ✓ |
| ReadEnterpriseOrEstablishmentByAddress           | `entities->searchByAddress()`            | ✓ |
| ReadEnterpriseOrEstablishmentByActivity          | `entities->searchByActivity()`           | ✓ |
| ReadStreetName                                   | `streets->search()`                      | ✓ |
| ReadCodeList                                     | `codeLists->authorizationSearchCodes()`, `codeLists->metaCodeList()` | ✓ |

This is an unofficial SDK. Feel free to submit a feature or pull request!

## Testing

```bash
composer test
```

The tests replay real KBO replies recorded with [Saloon's fixtures](https://docs.saloon.dev/the-basics/testing#fixtures)
under `tests/Fixtures/Saloon`, so they run offline without credentials. Natural persons' names are redacted.

To record a new or changed operation, copy `tests/.env.example` to `tests/.env`, fill in your credentials and the
environment (`test` or `production`), delete the fixture and run the tests again. CI sets `CI=true`, which makes a
missing fixture fail the build instead of recording it.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email support@masmerise.be instead of using the issue tracker.

## Credits

- [Muhammed Sari](https://github.com/mabdullahsari)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
