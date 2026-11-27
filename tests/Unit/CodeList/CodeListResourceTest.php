<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Unit\CodeList;

use KboPublicSearch\Authentication\Credentials;
use KboPublicSearch\CodeList\CodeListResource;
use KboPublicSearch\CodeList\Read\Request\ReadCodeListRequest;
use KboPublicSearch\Core\Failure\UnknownException;
use KboPublicSearch\Core\Http\KboConnector;
use KboPublicSearch\Environments\Environment;
use KboPublicSearch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

final class CodeListResourceTest extends TestCase
{
    #[Test]
    public function an_unknown_code_list_fails_the_reply(): void
    {
        // Arrange
        $resource = self::resource('<CodeListItem><Code>NEW_LIST</Code></CodeListItem>');

        // Assert
        $this->expectException(UnknownException::class);

        // Act
        $resource->metaCodeList();
    }

    #[Test]
    public function an_unknown_entity_kind_fails_the_reply(): void
    {
        // Arrange
        $resource = self::resource('<CodeListItem><Code>T005</Code><KeyValuePair><Key>EntityKind</Key><Value>branch</Value></KeyValuePair></CodeListItem>');

        // Assert
        $this->expectException(UnknownException::class);

        // Act
        $resource->authorizationSearchCodes();
    }

    private static function resource(string $items): CodeListResource
    {
        $reply = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Header>'
            . '<ReplyContext xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages"><RequestId>id</RequestId><ReplyId>rid</ReplyId>'
            . '<ReplyStatus><Code>10001</Code><Description>The operation completed successfully</Description></ReplyStatus></ReplyContext>'
            . '</soap:Header><soap:Body><ReadCodeListReply xmlns="http://economie.fgov.be/kbopub/webservices/v1/messages">'
            . $items
            . '</ReadCodeListReply></soap:Body></soap:Envelope>';

        $connector = new KboConnector(Credentials::present('userid', 'secret'));
        $connector->useEnvironment(Environment::Test);
        $connector->withMockClient(new MockClient([ReadCodeListRequest::class => MockResponse::make($reply, 200)]));

        return new CodeListResource($connector);
    }
}
