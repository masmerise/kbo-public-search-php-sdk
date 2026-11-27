<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\Core\Type\Primitive\StreetName;
use KboPublicSearch\Core\Type\Primitive\Zipcode;
use KboPublicSearch\Street\Reply\StreetNameReply;
use KboPublicSearch\Street\Search\Request\ReadStreetNameRequest;
use KboPublicSearch\Street\Type\Primitive\PartOfStreetName;
use KboPublicSearch\Tests\RecordedTestCase;
use PHPUnit\Framework\Attributes\Test;

final class StreetTest extends RecordedTestCase
{
    #[Test]
    public function search(): void
    {
        // Arrange
        $this->record(ReadStreetNameRequest::class, 'street/search');

        // Act
        $reply = $this->kbo->streets->search(Zipcode::fromString('9000'), PartOfStreetName::fromString('veld'));

        // Assert
        $this->assertInstanceOf(StreetNameReply::class, $reply);
        $this->assertContains('VELDSTRAAT', array_map(static fn (StreetName $name) => $name->toString(), $reply->streetNames->all()));
    }
}
