# KBO code tables

The spreadsheets in this folder are KBO's official code tables, downloaded on 2026-11-26 from
<https://economie.fgov.be/nl/themas/ondernemingen/kruispuntbank-van/diensten-voor-administraties/codetabellen>
(page last updated 27 April 2026). `generate.py` turns them into the SDK's code enums.

## Regenerating

1. Download the changed table over the file of the same name in this folder.
2. Run the generator (it needs `openpyxl` and `xlrd`):

   ```sh
   python3 -m venv /tmp/kbo-codes && /tmp/kbo-codes/bin/pip install openpyxl xlrd
   /tmp/kbo-codes/bin/python docs/codes/generate.py
   ```

3. Run `composer format` and `composer verify`, then review the diff. A case is renamed when its English
   description changes, which is a breaking change for callers.

Case names come from the English description. When two codes share a description (usually a retired code and its
successor), both get their code as a suffix, e.g. `Hairdresser20006` and `Hairdresser20301`.

## What is generated, and what is not

| Enum | Table |
|---|---|
| `Core\Type\Status\StatusCode` | legal / Status |
| `Core\Type\Denomination\DenominationCode` | identificatie / Denomination |
| `Core\Type\ContactInformation\ContactType` | contact / TypeContactData |
| `Core\Type\Activity\ActivityGroup` | quality-aut-activities / ActivityGroup |
| `Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOffCode` | strike-out / StrikeOut |
| `Core\Type\Address\TechnicalCreationReasonCode` | Technical-Data / TechnicalCreationReason |
| `Core\Type\Address\CountryCode` | identificatie / Country (NIS column) |
| `Enterprise\Type\JuridicalSituationCode` | legal / JuridicalSituation |
| `Enterprise\Type\EventCode` | legal / Event |
| `Enterprise\Type\JuridicalFormCode` | legal / JuridicalForm |
| `Enterprise\Type\FunctionCode` | legal / LegalFunction |
| `Enterprise\Type\ProfessionalCompetenceCode` | legal / Profession |
| `Enterprise\Type\LinkedEnterpriseCode` | legal / LinkedEnterprise |
| `Enterprise\Type\Currency` | financial / Currency |

Written by hand, because the web service uses its own codes there instead of the tables' codes:
`TypeOfAddressCode`, `ActivityClassificationCode`, `TypeOfEnterprise` (all enumerated in `docs/wsdl/datamodel.xsd`) and
`NacebelVersion`.

Value objects instead of enums, because their tables are large or grow often: `AuthorizationCode` (Permission),
`QualificationCode` (Quality), `MunicipalityCode` (NIS codes), `NacebelCode` and `StreetCode`.
