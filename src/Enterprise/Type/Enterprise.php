<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

use KboPublicSearch\Core\Type\Activity\Activities;
use KboPublicSearch\Core\Type\Address\Addresses;
use KboPublicSearch\Core\Type\Authorization\Authorizations;
use KboPublicSearch\Core\Type\ContactInformation\ContactInformations;
use KboPublicSearch\Core\Type\Denomination\Denominations;
use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOffs;
use KboPublicSearch\Core\Type\Period;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;

/** An enterprise as ReadEnterprise returns it. */
final readonly class Enterprise
{
    /** @internal */
    public function __construct(
        public EnterpriseNumber $number,
        public JuridicalSituation $juridicalSituation,
        public Period $period,
        /** In years, when the enterprise has a limited duration. */
        public ?int $duration,
        public TypeOfEnterprise $typeOfEnterprise,
        /** Only for legal persons. */
        public ?JuridicalForm $juridicalForm,
        public ?Capital $capital,
        /** Every name KBO holds, in every language. */
        public Denominations $denominations,
        /** The registered office; the XSD allows two. */
        public Addresses $addresses,
        public ContactInformations $contactInformation,
        /** The number of active establishment units. */
        public int $businessUnits,
        public Branches $branches,
        public ProfessionalCompetences $professionalCompetences,
        public Qualifications $qualifications,
        public Authorizations $authorizations,
        public Activities $activities,
        public Functions $functions,
        public LinkedEnterprises $linkedEnterprises,
        public ?FinancialData $financialData,
        public Replacements $replacements,
        public ExOfficioStrikingOffs $exOfficioStrikingOffs,
    ) {}
}
