<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Reply;

/** How KBO handled a request (ReplyContext > ReplyStatus > Code, cookbook §1.4). */
enum ReplyStatus: string
{
    case Success = '00001';
    case NoData = '00003';
    case EnterpriseNumberInvalid = '00005';
    case EstablishmentNumberInvalid = '00006';
    case JuridicalFormNotAllowed = '00007';
    case TooManyResults = '00008';
    case EnterpriseDoesNotExist = '00010';
    case EstablishmentDoesNotExist = '00011';
    case EnterpriseCanceled = '00014';
    case EstablishmentCanceled = '00015';
    case EnterpriseNotYetActive = '00016';
    case EstablishmentNotYetActive = '00017';
    case SuccessUncharged = '10001';
    case NoDataUncharged = '10003';

    /** A code this SDK does not know yet; the raw code is kept on ReplyContext::$statusCode. */
    case Unknown = 'unknown';

    public static function fromCode(string $code): self
    {
        return self::tryFrom($code) ?? self::Unknown;
    }

    /** Whether this reply was deducted from the daily request quota (cookbook §1.7). */
    public function charged(): bool
    {
        return match ($this) {
            self::Success, self::NoData, self::TooManyResults,
            self::EnterpriseCanceled, self::EstablishmentCanceled,
            self::EnterpriseNotYetActive, self::EstablishmentNotYetActive => true,
            default => false,
        };
    }

    public function isNoData(): bool
    {
        return $this === self::NoData || $this === self::NoDataUncharged;
    }

    public function isSuccess(): bool
    {
        return $this === self::Success || $this === self::SuccessUncharged;
    }

    /** More than 200 matches: the reply holds the first 200 only. */
    public function isTruncated(): bool
    {
        return $this === self::TooManyResults;
    }
}
