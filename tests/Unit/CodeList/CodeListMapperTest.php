<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\CodeList;

use KboPublicSearch\CodeList\Read\Request\ReadCodeListRequest;
use KboPublicSearch\CodeList\Type\CodeListId;
use KboPublicSearch\CodeList\Type\EntityKind;
use KboPublicSearch\CodeList\Type\Mapping\CodeListMapper;
use KboPublicSearch\Core\Xml\Reader;
use KboPublicSearch\Localization\Language;
use KboPublicSearch\Tests\Support\Message;
use KboPublicSearch\Tests\Support\Snippet;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use ValueError;

final class CodeListMapperTest extends TestCase
{
    #[Test]
    public function maps_the_cookbook_authorization_search_codes(): void
    {
        // Act
        $codes = $this->codeListMapper()->toAuthorizationSearchCodes(Snippet::load('reply-code-list'));
        $item = $codes->first();

        // Assert
        $this->assertCount(2, $codes);
        $this->assertSame('T005', $item?->code->toString());
        $this->assertSame('Aannemer ( Beroepsbekwaamheid algemeen aannemer in de bouwsector )', $item?->description->in(Language::Dutch));
        $this->assertSame(EntityKind::Enterprise, $item?->entityKind);
        $this->assertSame('T038', $codes[1]->code->toString());
        $this->assertCount(2, $codes->ofKind(EntityKind::Enterprise));
        $this->assertCount(0, $codes->ofKind(EntityKind::Establishment));
    }

    #[Test]
    public function unknown_entity_kind_fails(): void
    {
        // Assert
        $this->expectException(ValueError::class);

        // Act
        $this->codeListMapper()->toAuthorizationSearchCodes(self::items('<CodeListItem><Code>T005</Code><KeyValuePair><Key>EntityKind</Key><Value>branch</Value></KeyValuePair></CodeListItem>'));
    }

    #[Test]
    public function missing_entity_kind_fails(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The code list item T005 has no EntityKind.');

        // Act
        $this->codeListMapper()->toAuthorizationSearchCodes(self::items('<CodeListItem><Code>T005</Code></CodeListItem>'));
    }

    #[Test]
    public function maps_the_meta_code_list(): void
    {
        // Act
        $lists = $this->codeListMapper()->toMetaCodeList(self::items(
            '<CodeListItem><Code>AUTHORIZATION_SEARCH_CODE</Code></CodeListItem><CodeListItem><Code>META_CODE_LIST</Code></CodeListItem>',
        ));

        // Assert
        $this->assertSame([CodeListId::AuthorizationSearchCode, CodeListId::MetaCodeList], array_map(static fn ($item) => $item->code, $lists->all()));
    }

    #[Test]
    public function unknown_code_list_fails(): void
    {
        // Assert
        $this->expectException(ValueError::class);

        // Act
        $this->codeListMapper()->toMetaCodeList(self::items('<CodeListItem><Code>NEW_LIST</Code></CodeListItem>'));
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

    private static function items(string $items): \KboPublicSearch\Core\Xml\Element
    {
        return Reader::fromString('<ReadCodeListReply xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages">' . $items . '</ReadCodeListReply>');
    }

    private function codeListMapper(): CodeListMapper
    {
        return new CodeListMapper();
    }
}
