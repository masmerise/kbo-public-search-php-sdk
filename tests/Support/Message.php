<?php declare(strict_types=1);

namespace KboPublicSearch\Tests\Support;

use KboPublicSearch\Core\Http\Request;
use KboPublicSearch\Core\Http\Soap\Envelope;
use KboPublicSearch\Core\Xml\Element;
use KboPublicSearch\Core\Xml\Node;
use KboPublicSearch\Core\Xml\Reader;
use KboPublicSearch\Core\Xml\Writer;

/** Writes a request's message the way the envelope does and reads it back. */
final class Message
{
    public static function of(Request $request): Element
    {
        $xml = Writer::write(Node::element("mes:{$request->operation()}Request", $request->message()), Envelope::NAMESPACES);

        return Reader::fromString($xml);
    }

    /** @return list<string> */
    public static function names(Request $request): array
    {
        return array_map(static fn (Element $e) => $e->name(), self::of($request)->elements());
    }

    public static function xml(Request $request): string
    {
        return Writer::write(Node::element("mes:{$request->operation()}Request", $request->message()), Envelope::NAMESPACES);
    }
}
