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
use KboPublicSearch\Enterprise\Type\EnterpriseTypes;
use KboPublicSearch\Enterprise\Type\JuridicalFormFilter;
use KboPublicSearch\Enterprise\Type\Primitive\AuthorizationCode;
use KboPublicSearch\Enterprise\Type\Primitive\CompanyName;
use KboPublicSearch\Enterprise\Type\Primitive\FirstName;
use KboPublicSearch\Enterprise\Type\Primitive\LastName;

$enterprise = $kbo->enterprises->find(
    EnterpriseNumber::fromString('0314.595.348'), // also "BE0314595348", "314595348"
)->enterprise;

$enterprise->number->toString();               // "0314595348"
$enterprise->denominations->primary()?->name;  // the first name, in any language
$enterprise->juridicalSituation->status->kind; // StatusKind::Active
$enterprise->addresses->first()?->street->name->first();
$enterprise->functions;                        // Functions
$enterprise->activities->main();               // activities classified MAIN

// At most 200 summaries; $reply->isTruncated() when KBO found more.
$reply = $kbo->enterprises->searchByPhoneme(
    phonemes: Phonemes::of('economie', 'energie'),
    types: EnterpriseTypes::legalPersons(),    // ::naturalPersons(), ::all() (default)
    zipcode: Zipcode::fromString('1210'),
    juridicalForm: JuridicalFormFilter::Other, // only together with legalPersons()
    onlyActive: true,
);

$reply = $kbo->enterprises->searchByPersonName(LastName::fromString('Doe'), firstName: FirstName::fromString('John'));
$reply = $kbo->enterprises->searchByCompanyName(CompanyName::fromString('Federale Overheidsdienst Economie, K.M.O., Middenstand en Energie'));
$reply = $kbo->enterprises->searchByAuthorization(AuthorizationCode::fromString('T004'), LocationFilter::zipcodes('9300'));

foreach ($reply->enterprises as $summary) {
    echo $summary->number, ' ', $summary->name, PHP_EOL;
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
use KboPublicSearch\Core\Type\Parameter\Detail;
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Parameter\Phonemes;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Core\Type\Primitive\EstablishmentNumber;
use KboPublicSearch\Enterprise\Type\Primitive\AuthorizationCode;
use KboPublicSearch\Establishment\Type\EstablishmentName;

$establishment = $kbo->establishments->find(EstablishmentNumber::fromString('2.147.241.191'), Detail::Long)->establishment;

$kbo->establishments->ofEnterprise(EnterpriseNumber::fromString('0314595348'), Detail::Short);
$kbo->establishments->searchByName(EstablishmentName::fromString('Zuiderpoort Office Park'));
$kbo->establishments->searchByPhoneme(Phonemes::of('City', 'Atrium'));
$kbo->establishments->searchByAuthorization(AuthorizationCode::fromString('T004'), LocationFilter::municipalities('44021'));
```

`Detail::Short` returns the name in the requested languages (`$establishment->name`). `Detail::Long` returns every
denomination, the activities, the authorizations and the enterprise's ex officio striking offs instead. Whatever a level
does not return is an empty collection. Long replies are slow for enterprises with many establishments, so prefer
`ofEnterprise(..., Detail::Short)` followed by `find(..., Detail::Long)` for the ones you need.

### Enterprises and establishments together

```php
use KboPublicSearch\Core\Type\Parameter\LocationFilter;
use KboPublicSearch\Core\Type\Primitive\HouseNumber;
use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Entity\Type\ActivityCodes;
use KboPublicSearch\Entity\Type\EntityKinds;

$entities = $kbo->entities->searchByAddress(
    zipcode: Zipcode::fromString('1210'),
    street: StreetName::fromString('Vooruitgangstraat'),
    houseNumber: HouseNumber::fromString('50'),
)->entities;

$entities->enterprises();     // EnterpriseSummaries
$entities->establishments();  // Establishments

$kbo->entities->searchByActivity(
    activities: ActivityCodes::of('10711'),  // NACEBEL 2025 codes or prefixes (3-7 digits)
    location: LocationFilter::zipcodes('9300'),
    kinds: EntityKinds::legalPersons()->with(EntityKinds::establishments()), // ::all() (default), ::naturalPersons()
);
```

### Street names and code lists

```php
use KboPublicSearch\CodeList\Type\CodeListId;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Street\Type\PartOfStreetName;

$streets = $kbo->streets->search(Zipcode::fromString('9000'), PartOfStreetName::fromString('veld'))->streetNames;

$codes = $kbo->codeLists->read(CodeListId::AuthorizationSearchCode)->codeList;
$lists = $kbo->codeLists->read(CodeListId::MetaCodeList)->codeList; // which lists exist

foreach ($codes as $item) {
    echo $item->code, ' ', $item->description, ' ', $item->value('EntityKind'), PHP_EOL;
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
| `establishments->ofEnterprise()` / `searchBy*()`  | `EstablishmentSearchReply` | `establishments: Establishments`         |
| `entities->searchBy*()`                           | `EntitySearchReply`        | `entities: Entities`                     |
| `streets->search()`                               | `StreetNameReply`          | `streetNames: StreetNames`               |
| `codeLists->read()`                               | `CodeListReply`            | `codeList: CodeList`                     |

Searches return at most 200 results. When KBO found more, `$reply->isTruncated()` is `true`.
A search without matches returns an empty collection rather than throwing.

## Failures (exception handling)

The SDK uses exceptions as its medium to communicate failures.

```
KboPublicSearchException
├── CouldNotAuthenticate (wrong credentials)
│   └── MustAcceptTermsOfService (exposes ->url)
├── RequestLimitReached (daily quota used up)
├── CouldNotCompleteRequest (any other SOAP fault, exposes ->faultCode and ->faultString)
├── UnknownException (connection errors, replies that are not KBO replies)
└── ValidationException (KBO answered with a status this operation does not accept)
    ├── CouldNotFindEnterprise
    ├── CouldNotSearchEnterprisesByPhoneme
    ├── CouldNotSearchEnterprisesByPersonName
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
    $ex->context->statusCode; // "00014", also kept for codes the SDK does not know (ReplyStatus::Unknown)
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
surfaces as `CouldNotAuthenticate`. When credentials that worked before start failing, check the clock first.

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
| ReadEnterpriseByPhysicalPersonNameExactMatch     | `enterprises->searchByPersonName()`      | ✓ |
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
| ReadCodeList                                     | `codeLists->read()`                      | ✓ |

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
