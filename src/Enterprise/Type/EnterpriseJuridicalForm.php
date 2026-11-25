<?php declare(strict_types=1);

namespace KboPublicSearch\Enterprise\Type;

/** The juridical forms a search can be restricted to (messages.xsd JuridicalFormType, cookbook §2.2.1). */
enum EnterpriseJuridicalForm: string
{
    case CooperativeUnlimited = '006';
    case CooperativeLimited = '008';
    case GeneralPartnership = '011';
    case LimitedPartnershipSimple = '012';
    case PublicLimitedCompany = '014';
    case PrivateLimitedCompany = '015';
    case NonProfitAssociation = '017';
    case CoOwnersAssociation = '070';
    /** Private company (BV/SRL), including the former BVBA/SPRL. */
    case PrivateCompany = '610';
    /** Limited partnership (CommV/SComm), including the former simple limited partnership. */
    case LimitedPartnership = '612';
    case Cooperative = '706';
    case WithoutLegalPersonality = '721';
    /** Foreign entities and international public organisations. */
    case Foreign = 'ABO';
    case All = 'ALL';
    /** Every juridical form not listed above. */
    case Other = 'OVE';
}
