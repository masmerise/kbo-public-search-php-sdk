<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\Street;

use InvalidArgumentException;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Street\Search\Request\ReadStreetNameRequest;
use KboPublicSearch\Street\Type\Mapping\MapsStreetNames;
use KboPublicSearch\Street\Type\PartOfStreetName;
use KboPublicSearch\Tests\Support\Message;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class StreetTest extends TestCase
{
    use MapsStreetNames;

    #[Test]
    public function maps_the_cookbook_reply(): void
    {
        // Act
        $streetNames = $this->toStreetNames(Snippet::load('reply-street-names'));

        // Assert
        $this->assertCount(6, $streetNames);
        $this->assertSame('BASSEVELDESTRAAT', $streetNames->all()[0]);
        $this->assertSame('VELDSTRAAT', $streetNames[5]);
    }

    #[Test]
    public function request(): void
    {
        // Arrange
        $request = new ReadStreetNameRequest(Zipcode::fromString('9000'), PartOfStreetName::fromString('veld'));

        // Assert
        $this->assertSame('ReadStreetName', $request->operation());
        $this->assertStringContainsString('<mes:Zipcode>9000</mes:Zipcode><mes:PartOfStreetName>veld</mes:PartOfStreetName>', Message::xml($request));
    }

    #[Test]
    public function part_of_street_name_is_validated(): void
    {
        foreach ([fn () => PartOfStreetName::fromString('v'), fn () => PartOfStreetName::fromString(str_repeat('a', 33))] as $build) {
            try {
                $build();
                $this->fail('Expected InvalidArgumentException.');
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}
