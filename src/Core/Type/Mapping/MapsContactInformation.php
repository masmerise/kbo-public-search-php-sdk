<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Contact\ContactInformation;
use KboPublicSearch\Core\Type\Contact\ContactInformations;
use KboPublicSearch\Core\Type\Contact\ContactKind;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
trait MapsContactInformation
{
    use MapsLocalizedTexts;
    use MapsPeriods;

    private function toContactInformation(Element $contact): ContactInformation
    {
        $type = $contact->optionalText('ContactType');

        return new ContactInformation(
            type: $type,
            kind: transform($type, ContactKind::tryFrom(...)),
            description: $this->toLocalizedText($contact),
            value: $contact->optionalText('Value') ?? '',
            foreignBranchOffice: $contact->optionalText('ForeignBranchOfficeFlag') === 'true',
            since: $this->toDate($contact, 'Begin'),
        );
    }

    private function toContactInformations(Element $parent): ContactInformations
    {
        return ContactInformations::of(array_map($this->toContactInformation(...), $parent->children('ContactInformation')));
    }
}
