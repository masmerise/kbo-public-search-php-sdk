<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Recorded;

use KboPublicSearch\CodeList\Read\Request\ReadCodeListRequest;
use KboPublicSearch\CodeList\Reply\CodeListReply;
use KboPublicSearch\CodeList\Type\CodeListId;
use KboPublicSearch\CodeList\Type\CodeListItem;
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
        $reply = $this->kbo->codeLists->read(CodeListId::AuthorizationSearchCode);

        // Assert
        $this->assertInstanceOf(CodeListReply::class, $reply);
        $this->assertGreaterThan(0, $reply->codeList->count());
        $this->assertFalse($reply->context->charged());
        $this->assertContains('T004', array_map(static fn (CodeListItem $item) => $item->code, $reply->codeList->all()));
    }

    #[Test]
    public function meta_code_list(): void
    {
        // Arrange
        $this->record(ReadCodeListRequest::class, 'code-list/meta');

        // Act
        $reply = $this->kbo->codeLists->read(CodeListId::MetaCodeList);

        // Assert
        $this->assertContains('AUTHORIZATION_SEARCH_CODE', array_map(static fn (CodeListItem $item) => $item->code, $reply->codeList->all()));
    }
}
