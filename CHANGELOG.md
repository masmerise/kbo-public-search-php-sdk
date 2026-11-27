# Changelog

All notable changes to `kbo-public-search-php-sdk` will be documented in this file.

## 1.0.0 - 2026-11-27

Initial release, implementing cookbook R018.08:

- `enterprises->find()` (ReadEnterprise)
- `enterprises->searchByPhoneme()` (ReadEnterpriseByPhoneme)
- `enterprises->searchByPhysicalPersonName()` (ReadEnterpriseByPhysicalPersonNameExactMatch)
- `enterprises->searchByCompanyName()` (ReadEnterpriseLegalPersonByCompanyNameExactMatch)
- `enterprises->searchByAuthorization()` (ReadEnterpriseByAuthorization)
- `establishments->find()` (ReadEstablishmentByEstablishmentNumber)
- `establishments->ofEnterprise()` (ReadEstablishmentByEnterpriseNumber)
- `establishments->searchByName()` (ReadEstablishmentByNameExactMatch)
- `establishments->searchByPhoneme()` (ReadEstablishmentByPhoneme)
- `establishments->searchByAuthorization()` (ReadEstablishmentByAuthorization)
- `enterprisesOrEstablishments->searchByAddress()` (ReadEnterpriseOrEstablishmentByAddress)
- `enterprisesOrEstablishments->searchByActivity()` (ReadEnterpriseOrEstablishmentByActivity)
- `streets->search()` (ReadStreetName)
- `codeLists->authorizationSearchCodes()` and `codeLists->metaCodeList()` (ReadCodeList)
