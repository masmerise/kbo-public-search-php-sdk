<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\CodeList;

use KboPublicSearch\CodeList\Read\Request\ReadCodeListRequest;
use KboPublicSearch\CodeList\Type\CodeListId;
use KboPublicSearch\CodeList\Type\Mapping\MapsCodeLists;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Message;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MapsCodeListsTest extends TestCase
{
    use MapsCodeLists;

    #[Test]
    public function maps_the_cookbook_reply(): void
    {
        // Act
        $codeList = $this->toCodeList(Snippet::load('reply-code-list'));
        $item = $codeList->first();

        // Assert
        $this->assertCount(2, $codeList);
        $this->assertSame('T005', $item?->code);
        $this->assertSame('Aannemer ( Beroepsbekwaamheid algemeen aannemer in de bouwsector )', $item?->description->in(Language::Dutch));
        $this->assertSame('enterprise', $item?->value('EntityKind'));
        $this->assertNull($item?->value('Missing'));
        $this->assertSame('EntityKind', $item?->keyValuePairs[0]->key);
        $this->assertSame('T038', $codeList[1]->code);
    }

    #[Test]
    public function request(): void
    {
        // Arrange
        $request = new ReadCodeListRequest(CodeListId::AuthorizationSearchCode);

        // Assert
        $this->assertSame('ReadCodeList', $request->operation());
        $this->assertStringContainsString('<mes:CodeListId>AUTHORIZATION_SEARCH_CODE</mes:CodeListId>', Message::xml($request));
        $this->assertSame('META_CODE_LIST', CodeListId::MetaCodeList->value);
    }
}
