<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\ExOfficioStrikingOff\ExOfficioStrikingOffCode;
use KboPublicSearch\Core\Type\Mapping\ExOfficioStrikingOffMapper;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ExOfficioStrikingOffMapperTest extends TestCase
{
    #[Test]
    public function maps_code_description_and_date(): void
    {
        // Act
        $strikingOff = $this->exOfficioStrikingOffMapper()->toExOfficioStrikingOffs(Snippet::load('shared/ex-officio-striking-offs'))->first();

        // Assert
        $this->assertSame(ExOfficioStrikingOffCode::StrikingOffBasedOnAJudgment, $strikingOff?->code);
        $this->assertSame('niet-neerlegging jaarrekening', $strikingOff?->description->first());
        $this->assertSame('2013-05-15', $strikingOff?->validityPeriod?->begin?->toString());
    }

    private function exOfficioStrikingOffMapper(): ExOfficioStrikingOffMapper
    {
        return new ExOfficioStrikingOffMapper();
    }
}
