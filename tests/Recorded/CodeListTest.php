<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\CodeList\Read\Request\ReadCodeListRequest;
use KboPublicSearch\CodeList\Reply\AuthorizationSearchCodesReply;
use KboPublicSearch\CodeList\Type\AuthorizationSearchCodeItem;
use KboPublicSearch\CodeList\Type\CodeListId;
use KboPublicSearch\CodeList\Type\EntityKind;
use KboPublicSearch\CodeList\Type\MetaCodeListItem;
use KboPublicSearch\Tests\RecordedTestCase;
use PHPUnit\Framework\Attributes\Test;

final class CodeListTest extends RecordedTestCase
{
    #[Test]
    public function authorization_search_codes(): void
    {
        // Arrange
        $this->record(ReadCodeListRequest::class, 'code-list/authorization-search-code');

        // Act
        $reply = $this->kbo->codeLists->authorizationSearchCodes();

        // Assert
        $this->assertInstanceOf(AuthorizationSearchCodesReply::class, $reply);
        $this->assertCount(69, $reply->authorizationSearchCodes);
        $this->assertFalse($reply->context->charged());
        $this->assertContains('T004', array_map(static fn (AuthorizationSearchCodeItem $item) => $item->code->toString(), $reply->authorizationSearchCodes->all()));
        $this->assertCount(3, $reply->authorizationSearchCodes->ofKind(EntityKind::Establishment));
    }

    #[Test]
    public function meta_code_list(): void
    {
        // Arrange
        $this->record(ReadCodeListRequest::class, 'code-list/meta');

        // Act
        $reply = $this->kbo->codeLists->metaCodeList();

        // Assert
        $this->assertSame([CodeListId::AuthorizationSearchCode, CodeListId::MetaCodeList], array_map(static fn (MetaCodeListItem $item) => $item->code, $reply->metaCodeList->all()));
    }
}
