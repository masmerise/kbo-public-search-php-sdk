<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Reply;

use KboPublicSearch\Core\Reply\ReplyStatus;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ReplyStatusTest extends TestCase
{
    #[Test]
    public function known_and_unknown_codes(): void
    {
        $this->assertSame(ReplyStatus::EnterpriseCanceled, ReplyStatus::fromCode('00014'));
        $this->assertSame(ReplyStatus::Unknown, ReplyStatus::fromCode('99999'));
        $this->assertSame(ReplyStatus::Unknown, ReplyStatus::fromCode('unknown'));
    }

    #[Test]
    #[DataProvider('codes')]
    public function classifies_every_code(string $code, bool $success, bool $noData, bool $truncated, bool $charged): void
    {
        // Act
        $status = ReplyStatus::fromCode($code);

        // Assert
        $this->assertNotSame(ReplyStatus::Unknown, $status);
        $this->assertSame($success, $status->isSuccess(), 'isSuccess');
        $this->assertSame($noData, $status->isNoData(), 'isNoData');
        $this->assertSame($truncated, $status->isTruncated(), 'isTruncated');
        $this->assertSame($charged, $status->charged(), 'charged');
    }

    public static function codes(): iterable
    {
        //                   code     success noData truncated charged
        yield '00001' => ['00001', true, false, false, true];
        yield '00003' => ['00003', false, true, false, true];
        yield '00005' => ['00005', false, false, false, false];
        yield '00006' => ['00006', false, false, false, false];
        yield '00007' => ['00007', false, false, false, false];
        yield '00008' => ['00008', false, false, true, true];
        yield '00010' => ['00010', false, false, false, false];
        yield '00011' => ['00011', false, false, false, false];
        yield '00014' => ['00014', false, false, false, true];
        yield '00015' => ['00015', false, false, false, true];
        yield '00016' => ['00016', false, false, false, true];
        yield '00017' => ['00017', false, false, false, true];
        yield '10001' => ['10001', true, false, false, false];
        yield '10003' => ['10003', false, true, false, false];
    }
}
