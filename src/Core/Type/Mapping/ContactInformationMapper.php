<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Type\Mapping;

use KboPublicSearch\Core\Type\ContactInformation\ContactInformation;
use KboPublicSearch\Core\Type\ContactInformation\ContactInformations;
use KboPublicSearch\Core\Type\ContactInformation\ContactType;
use KboPublicSearch\Core\Xml\Element;

use function KboPublicSearch\Core\Utility\transform;

/** @internal */
final readonly class ContactInformationMapper
{
    public function __construct(
        private DescriptionMapper $descriptionMapper = new DescriptionMapper(),
        private PeriodMapper $periodMapper = new PeriodMapper(),
    ) {}

    private function toContactInformation(Element $contact): ContactInformation
    {
        return new ContactInformation(
            contactType: transform($contact->optionalText('ContactType'), ContactType::from(...)),
            description: $this->descriptionMapper->toDescription($contact),
            value: $contact->optionalText('Value') ?? '',
            foreignBranchOfficeFlag: $contact->optionalText('ForeignBranchOfficeFlag') === 'true',
            begin: $this->periodMapper->toDate($contact, 'Begin'),
        );
    }

    public function toContactInformations(Element $parent): ContactInformations
    {
        return ContactInformations::of(array_map($this->toContactInformation(...), $parent->children('ContactInformation')));
    }
}
