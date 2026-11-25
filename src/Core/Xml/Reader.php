<?php declare(strict_types=1);

namespace KboPublicSearch\Core\Xml;

use DOMDocument;
use RuntimeException;

/** @internal */
final readonly class Reader
{
    /** @throws RuntimeException */
    public static function fromString(string $xml): Element
    {
        if (trim($xml) === '') {
            throw new RuntimeException('Response is not valid XML.');
        }

        $previous = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument();
            $loaded = $document->loadXML($xml, LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if (! $loaded || $document->documentElement === null) {
            throw new RuntimeException('Response is not valid XML.');
        }

        // SOAP forbids DTDs; refusing them keeps entity tricks out of every reply.
        if ($document->doctype !== null) {
            throw new RuntimeException('Response must not contain a document type declaration.');
        }

        return new Element($document->documentElement);
    }
}
