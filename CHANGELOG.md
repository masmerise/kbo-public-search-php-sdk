# Changelog

All notable changes to `kbo-public-search-php-sdk` will be documented in this file.

## 1.0.0 - unreleased

Initial release, implementing cookbook R018.08:

- `enterprises->find()` (ReadEnterprise)
- `enterprises->searchByPhoneme()` (ReadEnterpriseByPhoneme)
- `enterprises->searchByPersonName()` (ReadEnterpriseByPhysicalPersonNameExactMatch)
- `enterprises->searchByCompanyName()` (ReadEnterpriseLegalPersonByCompanyNameExactMatch)
- `enterprises->searchByAuthorization()` (ReadEnterpriseByAuthorization)
- `establishments->find()` (ReadEstablishmentByEstablishmentNumber)
- `establishments->ofEnterprise()` (ReadEstablishmentByEnterpriseNumber)
- `establishments->searchByName()` (ReadEstablishmentByNameExactMatch)
- `establishments->searchByPhoneme()` (ReadEstablishmentByPhoneme)
- `establishments->searchByAuthorization()` (ReadEstablishmentByAuthorization)
- `entities->searchByAddress()` (ReadEnterpriseOrEstablishmentByAddress)
- `entities->searchByActivity()` (ReadEnterpriseOrEstablishmentByActivity)
- `streets->search()` (ReadStreetName)
- `codeLists->read()` (ReadCodeList)
