<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\Core\Reply\ReplyStatus;
use KboPublicSearch\Core\Type\Primitive\EnterpriseNumber;
use KboPublicSearch\Enterprise\Find\Failure\CouldNotFindEnterprise;
use KboPublicSearch\Enterprise\Find\Request\ReadEnterpriseRequest;
use KboPublicSearch\Enterprise\Reply\EnterpriseReply;
use KboPublicSearch\Enterprise\Type\TypeOfEnterprise;
use KboPublicSearch\Tests\RecordedTestCase;
use PHPUnit\Framework\Attributes\Test;

final class EnterpriseTest extends RecordedTestCase
{
    #[Test]
    public function find(): void
    {
        // Arrange
        $this->record(ReadEnterpriseRequest::class, 'enterprise/find');
        $number = EnterpriseNumber::fromString(self::ENTERPRISE);

        // Act
        $reply = $this->kbo->enterprises->find($number);

        // Assert
        $this->assertInstanceOf(EnterpriseReply::class, $reply);
        $this->assertTrue($reply->enterprise->number->equals($number));
        $this->assertTrue($reply->context->status->isSuccess());
        $this->assertTrue($reply->context->charged());
        $this->assertNotNull($reply->context->accountBalance);
        $this->assertNotNull($reply->context->snapshotDate);
        $this->assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $reply->context->requestId);

        // Everything in the recording is mapped.
        $enterprise = $reply->enterprise;
        $this->assertSame(TypeOfEnterprise::NaturalPerson, $enterprise->type);
        $this->assertSame('000', $enterprise->juridicalSituation->code);
        $this->assertSame('2026-05-11', $enterprise->period->begin?->toString());
        $this->assertNull($enterprise->juridicalForm);
        $this->assertSame('', $enterprise->denominations->primary()?->language);
        $this->assertSame(1, $enterprise->businessUnits);
        $this->assertSame(0, $enterprise->addresses->count());
        $this->assertSame(2, $enterprise->qualifications->count());
        $this->assertSame(2, $enterprise->activities->count());
        $this->assertSame('62100', $enterprise->activities->main()->first()?->code);
        $this->assertSame(1, $enterprise->functions->count());
        $this->assertNotNull($enterprise->functions->first()?->person);
    }

    #[Test]
    public function find_unknown_number(): void
    {
        // Arrange
        $this->record(ReadEnterpriseRequest::class, 'enterprise/find-unknown');

        // Act
        try {
            $this->kbo->enterprises->find(EnterpriseNumber::fromString('0000000097'));
            $this->fail('Expected CouldNotFindEnterprise.');
        } catch (CouldNotFindEnterprise $ex) {
            // Assert
            $this->assertSame(ReplyStatus::EnterpriseDoesNotExist, $ex->status);
            $this->assertSame(10, $ex->getCode());
        }
    }

    #[Test]
    public function find_canceled(): void
    {
        // Arrange
        $this->record(ReadEnterpriseRequest::class, 'enterprise/find-canceled');

        // Act
        try {
            $this->kbo->enterprises->find(EnterpriseNumber::fromString('0206231995'));
            $this->fail('Expected CouldNotFindEnterprise.');
        } catch (CouldNotFindEnterprise $ex) {
            // Assert
            $this->assertSame(ReplyStatus::EnterpriseCanceled, $ex->status);
            $this->assertTrue($ex->context->charged());
        }
    }
}
