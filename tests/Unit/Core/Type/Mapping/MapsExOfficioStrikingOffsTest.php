<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Core\Type\Mapping;

use KboPublicSearch\Core\Type\Mapping\MapsExOfficioStrikingOffs;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsExOfficioStrikingOffsTest extends TestCase
{
    use MapsExOfficioStrikingOffs;

    #[Test]
    public function maps_code_description_and_date(): void
    {
        // Act
        $strikingOff = $this->toExOfficioStrikingOffs(Snippet::load('shared/ex-officio-striking-offs'))->first();

        // Assert
        $this->assertSame('001', $strikingOff?->code);
        $this->assertSame('niet-neerlegging jaarrekening', $strikingOff?->description->first());
        $this->assertSame('2013-05-15', $strikingOff?->since?->toString());
    }
}
